<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Memcrab\Access\Access;
use Memcrab\Access\AccessException;

/**
 *  Corresponding Class to test Access class
 *
 *  For each class in your library, there should be a corresponding Unit-Test for it
 *  Unit-Tests should be as much as possible independent from other test going on.
 *
 *  @author Oleksandr Diudiun
 */
class AccessTest extends \PHPUnit\Framework\TestCase {
	protected $Access;
	protected $yaml;
	protected $parsedRules;

	protected function setUp() {
		$this->yaml = __DIR__ . "/../src/rules.example.yaml";
		$this->Access = new Access();
		$this->parsedRules = yaml_parse_file($this->yaml, 0);
	}

	protected function tearDown() {
		unset($this->Access);
		unset($this->yaml);
		unset($this->parsedRules);
		unset($this->rulesExample);
	}

	public function testRoutesFileExist() {
		$this->assertFileExists($this->yaml, "rules.example.yaml not found in " . $this->yaml);
	}

	public function testInitializeRights() {
		$this->assertNotEmpty($this->parsedRules);
		$this->Access->loadRules($this->parsedRules);

		foreach ($this->parsedRules as $groupName => $accessRule) {
			$this->assertArrayHasKey('services', $accessRule);
			$this->assertArrayHasKey('roles', $accessRule);
			foreach ($accessRule['services'] as $serviceName => $actions) {
				foreach ($actions as $action) {
					foreach ($accessRule['roles'] as $role) {
						$this->assertTrue(isset($this->Access->getRightsByServices()[$serviceName][$action][$role]));
						$this->assertTrue(isset($this->Access->getRightsByGroups()[$groupName][$role][$serviceName]));
						$this->assertTrue(isset($this->Access->getRightsByRoles()[$role][$groupName][$serviceName]));
					}
				}
			}
		}
	}

	public function rulesProvider() {
		return array(
			array(array()),
			array(array("contentView")),
			array(array("contentView" => array())),
			array(array("contentView" => array("roles"))),
			array(array("contentView" => array("roles" => array()))),
			array(array("contentView" => array("roles" => array()))),
			array(array("contentView" => array("roles" => array("guest"), "services"))),
			array(array("contentView" => array("roles" => array("guest"), "services" => array()))),
		);
	}

	/**
	 * @dataProvider rulesProvider
	 */
	public function testInitializationRightsException(array $rules) {
		$this->expectException(AccessException::class);
		$this->Access->loadRules($rules);
	}

	public function requestsProvider() {
		return array(
			array("", "", "", false),
			array("post", "", "", false),
			array("post", "get", "", false),
			array("", "get", "guest", false),
			array("postFail", "get", "guest", false),
			array("post", "getFail", "guest", false),
			array("post", "get", "guestFail", false),
			array("post", "get", "guest", true),
			array("post", "save", "admin", true),
		);
	}

	/**
	 * @dataProvider requestsProvider
	 */
	public function testCheckRights(string $service, string $action, string $userRole, bool $result) {
		$this->Access->loadRules($this->parsedRules);
		$this->assertEquals($result, $this->Access->checkRights($service, $action, $userRole));
	}

	public function testGetRoleAllowedAccessGroups() {
		$this->Access->loadRules($this->parsedRules);
		$this->assertContains("contentManage", $this->Access->getRoleAllowedAccessGroups("admin"));
	}

	public function testGetRoleAllowedAccessGroupsException() {
		$this->Access->loadRules($this->parsedRules);
		$this->expectException(AccessException::class);
		$this->Access->getRoleAllowedAccessGroups("adminFailName");
	}
}