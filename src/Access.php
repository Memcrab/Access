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
	private $servicesMatrix;
	private $groupsMatrix;
	private $rolesMatrix;
	private $rules;

	public function loadRules(array $rules): void {
		if (empty($rules)) {
			throw new AccessException(_("Empty rules"), 500);
		}

		$this->rules = $rules;

		foreach ($this->rules as $groupName => $accessRule) {
			if (empty($accessRule['roles']) || !is_array($accessRule['roles'])) {
				throw new AccessException(_("Empty roles in group:") . " " . $groupName, 500);
			}
			if (empty($accessRule['services']) || !is_array($accessRule['services'])) {
				throw new AccessException(_("Empty services in group:") . " " . $groupName, 500);
			}

			foreach ($accessRule['services'] as $serviceName => $actions) {
				if (empty($actions) || !is_array($actions)) {
					throw new AccessException(_("Empty actions in service:") . " " . $serviceName, 500);
				}
				foreach ($actions as $action) {
					foreach ($accessRule['roles'] as $role) {
						$this->servicesMatrix[$serviceName][$action][$role][] = $groupName;
						$this->groupsMatrix[$groupName][$role][$serviceName][] = $action;
						$this->rolesMatrix[$role][$groupName][$serviceName][] = $action;
					}
				}
			}
		}
	}

	public function checkRights(string $service, string $action, string $userRole): bool {
		return isset($this->servicesMatrix[$service][$action][$userRole]);
	}

	public function getRoleAllowedAccessGroups(string $role): array{
		$allowedAccessGroups = array();
		if (!isset($this->rolesMatrix[$role])) {
			throw new AccessException(_("Role not exist: ") . " " . $role, 401);
		}

		if (is_array($this->rolesMatrix[$role])) {
			return array_keys($this->rolesMatrix[$role]);
		} else {
			return array();
		}
	}

	public function getRightsByServices(): array{
		return $this->servicesMatrix;
	}

	public function getRightsByRoles(): array{
		return $this->rolesMatrix;
	}

	public function getRightsByGroups(): array{
		return $this->groupsMatrix;
	}
}