<?php
declare (strict_types = 1);
namespace memCrab\Access;

use memCrab\Exceptions\AccessException;

/**
 *  Router for core project
 *
 *  @author Oleksandr Diudiun
 */
class Access {
	private $accessMatrix;
	private $groupsMatrix;

	public function loadRules(array $rules) {
		if (empty($rules)) {
			throw new AccessException(_("Empty rules"), 1);
		}

		foreach ($rules as $group => $access) {
			if (empty($access['roles']) || !is_array($access['roles'])) {
				throw new AccessException(_("Empty roles in group:") . " " . $group, 401);
			}
			if (empty($access['services']) || !is_array($access['services'])) {
				throw new AccessException(_("Empty services in group:") . " " . $group, 401);
			}

			foreach ($access['services'] as $service) {
				if (empty($service) || !is_array($service)) {
					throw new AccessException(_("Empty actions in service:") . " " . $service, 401);
				}
				foreach ($service as $action) {
					foreach ($access['roles'] as $role) {
						$this->accessMatrix[$service][$action][$role][] = $group;
						$this->groupsMatrix[$group][$role][$service][] = $action;
					}
				}
			}
		}
	}

	public function checkRights($service, $action, $userRole) {
		return isset($this->accessMatrix[$service][$action][$userRole]);
		// throw new AccessException(_("Undefined Service:") . " " . $service, 401);
	}

	public function getUserAllowedAccessGroups($userRole) {
		$allowedAccessGroups = array();

		if (in_array($userRole, self::$availableRoles)) {
			foreach ($this->groups as $group) {
				if (in_array($userRole, $group["roles"])) {
					$allowedAccessGroups[$group['name']] = $group;
				}
			}

		}

		return $allowedAccessGroups;
	}

	public function getModels() {return $this->models;}
}