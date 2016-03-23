<?php
/**
 * Module Login
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Package\Crud\Web\Module;

use \Skeleton\Core\Web\Template;
use \Skeleton\Core\Web\Module;
use \Skeleton\Core\Web\Session;
use \Skeleton\Pager\Web\Pager;

abstract class Crud extends Module {

	/**
	 * Login required ?
	 * Default = yes
	 *
	 * @access public
	 * @var bool $login_required
	 */
	public $login_required = false;

	/**
	 * Template to use
	 *
	 * @access public
	 * @var string $template
	 */
	public $template = '@skeleton-package-crud\content.twig';

	/**
	 * Display method
	 *
	 * @access public
	 */
	public function display() {
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
			if (substr($definition['field'], -3) == '_id') {
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
	public function display_edit() {
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
			if (substr($definition['field'], -3) == '_id') {
				unset($fields[$key]);
			}
			if ($definition['field'] == 'id') {
				unset($fields[$key]);
			}
		}
		$template->assign('default_fields', $fields);

		if (isset($_POST['object'])) {
			$object->load_array($_POST['object']);
			if (is_callable( [$object, 'validate'] )) {
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
	public function display_create() {
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
			$object->save();
			Session::redirect($this->get_module_path() . '?action=edit&id=' . $object->id);
		}

		$template->assign('pager', $pager);

		$fields = $classname::get_object_fields();
		foreach ($fields as $key => $definition) {
			if (substr($definition['field'], -3) == '_id') {
				unset($fields[$key]);
			}
			if ($definition['field'] == 'id') {
				unset($fields[$key]);
			}
		}
		$template->assign('default_fields', $fields);
	}

	/**
	 * Is deletable
	 *
	 * @access public
	 * @param Object $object
	 * @return bool $deletable
	 */
	public function is_deletable($object) {
		return true;
	}

	/**
	 * Is editable
	 *
	 * @access public
	 * @param Object $object
	 * @return bool $editable
	 */
	public function is_editable($object) {
		return true;
	}

	/**
	 * is creatable
	 *
	 * @access public
	 * @return bool $creatable
	 */
	public function is_creatable() {
		return true;
	}

	/**
	 * Delete
	 *
	 * @access public
	 */
	public function display_delete() {
		/**
		 * Get the pager
		 */
		$pager = $this->get_pager();
		$classname = $pager->get_classname();
		$object = $classname::get_by_id($_GET['id']);
		$object->delete();
		Session::redirect($this->get_module_path());
	}

	/**
	 * Get pager
	 *
	 * @access public
	 * @return Skeleton\Pager\Web\Pager $pager
	 */
	abstract public function get_pager();


}
