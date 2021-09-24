<?php
declare (strict_types = 1);
namespace Memcrab\Access;

use Memcrab\Access\AccessException;

/**
 *  Router for core project
 *
 *  @author Oleksandr Diudiun
 */
class Access {
	private $rules;
	private $accessMatrix;

    private static $accessNamesMatrix = [];

    public function loadRules(array $rules): void {
		if (empty($rules)) {
			throw new AccessException(_("Empty rules"), 500);
		}

		$this->rules = $rules;
		$this->accessMatrix = array();

		foreach ($this->rules as $groupName => $accessRule) {
			if (empty($accessRule['roles'])) {
				throw new AccessException(_("Empty roles in Access Group:") . " " . $groupName, 500);
			}
			if (empty($accessRule['services'])) {
				throw new AccessException(_("Empty services in Access Group:") . " " . $groupName, 500);
			}
            if (empty($accessRule['names'])) {
                throw new AccessException(_("Empty names in Access Group:") . " " . $groupName, 500);
            }

			$this->accessMatrix = array_merge_recursive(
				$this->accessMatrix,
				array_fill_keys($accessRule['roles'], $accessRule['services'])
			);

            self::$accessNamesMatrix = array_merge_recursive(
                self::$accessNamesMatrix,
                array_fill_keys($accessRule['roles'], $accessRule['names'])
            );
		}
	}

	public function checkRights(string $role, string $service, string $action): bool{
		$Ref = new \ReflectionClass($service);
		if (isset($this->accessMatrix[$role][$Ref->getShortName()])) {
			return in_array($action, $this->accessMatrix[$role][$Ref->getShortName()]);
		} else {
			return false;
		}
	}

    public static function getNamesByRole(string $role): array
    {
        return isset(self::$accessNamesMatrix[$role]) ? self::$accessNamesMatrix[$role] : [];
    }
}
