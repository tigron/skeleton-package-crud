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
		{% embed "@skeleton-package-crud/content.twig" with {'object_name': 'My object'} %}{% endembed %}
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

Filters for pager

	{% block pager_filters %}
		<div class="form-group">
			<label class="control-label col-lg-2">{% trans "Search" %} </label>
			<div class="controls col-lg-10">
				<input type="text" class="form-control" name="search" value="{{ pager.get_search() }}"/>
			</div>
		</div>
	{% endblock pager_filters %}

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

Form for object creation

	{% block form_create %}
		{#
			Insert a complete form for object creation here.
			Make sure that an array with key object[field] is posted
		#}
	{% endblock form_create %}

Form for edit object

	{% block form_edit %}
		{#
			Insert a complete form for editing the object here.
			Make sure that an array with key object[field] is posted
		#}
	{% endblock form_edit %}

Page Create

	{% block page_create %}
		{#
			Insert a complete page for creating the object here
			Make sure that an array with key object[field] is posted
		#}
	{% endblock page_create %}

Page Edit

	{% block page_edit %}
		{#
			Insert a complete page for editing the object here
			Make sure that an array with key object[field] is posted
		#}
	{% endblock page_edit %}

Page Edit Footer

	{% block page_edit_footer %}
		{#
			This is a part of block page_edit just below the edit form
			This can be used to show additional information about the object
		#}
	{% endblock page_edit_footer %}

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
