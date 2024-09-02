<?php

declare(strict_types=1);

/**
 * Module Login
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Package\Crud\Web\Module;

use Skeleton\Application\Web\Module;
use Skeleton\Application\Web\Template;
use Skeleton\Core\Http\Session;

abstract class Crud extends Module {
	/**
	 * Login required ?
	 * Default = yes
	 *
	 * @access protected
	 */
	protected bool $login_required = false;

	/**
	 * Template to use
	 *
	 * @access protected
	 */
	protected ?string $template = '@skeleton-package-crud\content.twig';

	/**
	 * Display method
	 *
	 * @access public
	 */
	public function display(): void {
		/**
		 * Initialize the template object
		 */
		$template = Template::Get();

		/**
		 * Get the pager object
		 */
		$pager = $this->get_pager();

		if (isset($_POST['search'])) {
			$pager->set_search($_POST['search']);
		}

		$pager->page();
		$template->assign('pager', $pager);

		/**
		 * Find the deletables
		 */
		$deletables = [];
		foreach ($pager->items as $item) {
			if ($this->is_deletable($item)) {
				$deletables[] = $item->id;
			}
		}
		$template->assign('deletables', $deletables);

		/**
		 * Find the editables
		 */
		$editables = [];
		foreach ($pager->items as $item) {
			if ($this->is_editable($item)) {
				$editables[] = $item->id;
			}
		}
		$template->assign('editables', $editables);

		/**
		 * Get default fields for pager
		 */
		$classname = $pager->get_classname();
		$fields = $classname::get_object_fields();

		foreach ($fields as $key => $definition) {
			if (substr($definition['Field'], -3) === '_id') {
				unset($fields[$key]);
			}
		}

		/**
		 * Creatable
		 */
		$template->assign('creatable', $this->is_creatable());

		$template->assign('default_fields', $fields);
	}

	/**
	 * Edit
	 *
	 * @access public
	 */
	public function display_edit(): void {
		$template = Template::get();

		/**
		 * Get the object
		 */
		$pager = $this->get_pager();
		$classname = $pager->get_classname();
		$object = $classname::get_by_id($_GET['id']);
		$template->assign('object', $object);
		$template->assign('pager', $pager);

		$fields = $classname::get_object_fields();
		foreach ($fields as $key => $definition) {
			if (substr($definition['Field'], -3) === '_id') {
				unset($fields[$key]);
			}
			if ($definition['Field'] === 'id') {
				unset($fields[$key]);
			}
		}
		$template->assign('default_fields', $fields);

		if (isset($_POST['object'])) {
			$object->load_array($_POST['object']);
			if (is_callable([$object, 'validate'])) {
				$object->validate($errors);
			} else {
				$errors = [];
			}

			if (count($errors) > 0) {
				$template->assign('errors', $errors);
			} else {
				$object->save();
				Session::set_sticky('message', 'object_updated');
				Session::redirect($this->get_module_path() . '?action=edit&id=' . $object->id);
			}
		}
	}

	/**
	 * Create
	 *
	 * @access public
	 */
	public function display_create(): void {
		if (!$this->is_creatable()) {
			throw new \Exception('Cannot create object. Not allowed');
		}

		$template = Template::get();

		/**
		 * Get the object
		 */
		$pager = $this->get_pager();
		$classname = $pager->get_classname();

		if (isset($_POST['object'])) {
			$object = new $classname();
			$object->load_array($_POST['object']);
			if (is_callable([$object, 'validate'])) {
				$object->validate($errors);
			} else {
				$errors = [];
			}
			if (count($errors) > 0) {
				$template->assign('errors', $errors);
			} else {
				$object->save();
				if ($this->is_editable($object)) {
					Session::redirect($this->get_module_path() . '?action=edit&id=' . $object->id);
				} else {
					Session::redirect($this->get_module_path());
				}
			}
		}

		$template->assign('pager', $pager);

		$fields = $classname::get_object_fields();
		foreach ($fields as $key => $definition) {
			if (substr($definition['Field'], -3) === '_id') {
				unset($fields[$key]);
			}
			if ($definition['Field'] === 'id') {
				unset($fields[$key]);
			}
		}
		$template->assign('default_fields', $fields);
	}

	/**
	 * Is deletable
	 *
	 * @access public
	 * @return bool $deletable
	 */
	public function is_deletable(object $object): bool {
		return true;
	}

	/**
	 * Is editable
	 *
	 * @access public
	 * @return bool $editable
	 */
	public function is_editable(object $object): bool {
		return true;
	}

	/**
	 * is creatable
	 *
	 * @access public
	 * @return bool $creatable
	 */
	public function is_creatable(): bool {
		return true;
	}

	/**
	 * Delete
	 *
	 * @access public
	 */
	public function display_delete(): void {
		/**
		 * Get the pager
		 */
		$pager = $this->get_pager();
		$classname = $pager->get_classname();
		$object = $classname::get_by_id($_GET['id']);
		$object->delete();
		Session::set_sticky('message', 'object_deleted');
		Session::redirect($this->get_module_path());
	}

	/**
	 * Get pager
	 *
	 * @access public
	 */
	abstract public function get_pager(): \Skeleton\Pager\Web\Pager;
}
