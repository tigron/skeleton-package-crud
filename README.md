# skeleton-package-crud

## Description

This library enables CRUD functionality for skeleton objects


## Installation

Installation via composer:

    composer require tigron/skeleton-package-crud

## Howto

Create a module in your application that extends from Skeleton\Package\Web\Module\Crud

    <?php
	/**
	 * Module Crud
	 *
	 * @author Christophe Gosiau <christophe@tigron.be>
	 * @author Gerry Demaret <gerry@tigron.be>
	 * @author David Vandemaele <david@tigron.be>
	 */

	use Skeleton\Package\Crud\Web\Module\Crud

	class Web_Module_User extends Crud {

		/**
		 * The template
		 *
		 * @access public
		 */
		public $template = 'user.twig';
	}

Create a template for your module that injects the generated templates into your layout

	{% extends "_default/layout.base.twig" %}


	{% block header_js %}
		{% embed "@skeleton-package-crud/javascript.twig" %}{% endembed %}
	{% endblock header_js %}

	{% block header_css %}
		{% embed "@skeleton-package-crud/css.twig" %}{% endembed %}
	{% endblock header_css %}

	{% block content %}
		{% embed "@skeleton-package-crud/content.twig" %}{% endembed %}
	{% endblock content %}


Create a route in your application Config.php

	/**
	 * Routes
	 */
	'routes' => [
		'web_module_user' => [
			'$language/user'
		],
	]

In embed "@skeleton-package-crud/content.twig there are blocks available to customize the CRUD module.
Below you can find the list of blocks with their default content:

Table header for pager

	{% block pager_table_head %}
		{% for definition in default_fields %}
			<th>{{ pager.create_header(definition.field, definition.field) }}</th>
		{% endfor %}
	{% endblock pager_table_head %}

Table row for pager

	{% block pager_table_row %}
		{% for definition in default_fields %}
			<td>{{ attribute(object, definition.field) }}</td>
		{% endfor %}
	{% endblock pager_table_row %}

In the module, you can use the following methods to configure the CRUD behavior

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
