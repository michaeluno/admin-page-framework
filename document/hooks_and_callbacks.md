The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.

## AdminPageFramework ##

### Methods and Action Hooks ###

* `start_ + extended class name` – triggered at the end of the class constructor.
* `do_before_ + page slug` – triggered before rendering the page.
* `do_ + page slug` – triggered in the middle of rendering the page.
* `do_after_ + page slug` – triggered after rendering the page.
* `do_before_ + page slug + _ + tab slug` – triggered before rendering the page.
* `do_ + page slug + _ + tab slug` – triggered in the middle of rendering the page.
* `do_after_ + page slug + _ + tab slug` – triggered after rendering the page.
* `do_before_ + extended class name` – triggered before rendering the page. It applies to all pages created by the instantiated class object.
* `do_ + extended class name` – triggered in the middle of rendering the page. It applies to all pages created by the instantiated class object.
* `do_after_ + extended class name` – triggered after rendering the page. It applies to all pages created by the instantiated class object.

### Methods and Filters ###

* `head_ + page slug` – receives the output of the top part of the page.
* `head_ + page slug + _ + tab slug` – receives the output of the top part of the page.
* `head_ + extended class name` – receives the output of the top part of the page, applied to all pages created by the instantiated class object.
* `content_ + page slug` – receives the output of the middle part of the page including form input fields.
* `content_ + page slug + _ + tab slug` – receives the output of the middle part of the page including form input fields.
* `content_ + extended class name` – receives the output of the middle part of the page, applied to all pages created by the instantiated class object.
* `foot_ + page slug` – receives the output of the bottom part of the page.
* `foot_ + page slug + _ + tab slug` – receives the output of the bottom part of the page.
* `foot_ + extended class name` – receives the output of the bottom part of the page, applied to all pages created by the instantiated class object.
* `extended class name + section_ + section ID` – receives the description output of the given form section ID. The first parameter: output string. The second parameter: the array of option.
* `extended class name + field_ + field ID` – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.
* `validation_ + extended class name` – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.
* `validation_ + page slug` – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.
* `validation_ + page slug + _ + tab slug` – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.
* `style_ + page slug + _ + tab slug` – receives the output of the CSS rules applied to the tab page of the slug.
* `style_ + page slug` – receives the output of the CSS rules applied to the page of the slug.
* `style_ + extended class name` – receives the output of the CSS rules applied to the pages added by the instantiated class object.
* `script_ + page slug + _ + tab slug` – receives the output of the JavaScript script applied to the tab page of the slug.
* `script_ + page slug` – receives the output of the JavaScript script applied to the page of the slug.
* `script_ + extended class name` – receives the output of the JavaScript script applied to the pages added by the instantiated class object.
* `export_ + page slug + _ + tab slug` – receives the exporting array sent from the tab page.
* `export_ + page slug` – receives the exporting array submitted from the page.
* `export_ + extended class name` – receives the exporting array submitted from the plugin.
* `import_ + page slug + _ + tab slug` – receives the importing array submitted from the tab page.
* `import_ + page slug` – receives the importing array submitted from the page.
* `import_ + extended class name` – receives the importing array submitted from the plugin.

## AdminPageFramework_PostType ##

### Methods and Action Hooks ###
* `start_ + extended class name` – triggered at the end of the class constructor.

### Methods and Filters ###
* `cell_ + post type + column key` - receives the output string for the listing table of the custom post type's post. The first parameter: output string. The second parameter: the post ID.

## AdminPageFramework_MetaBox ###

### Methods and Action Hooks ###
* `start_ + extended class name` – triggered at the end of the class constructor.

### Methods and Filters ###


## Remarks ##
The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.

## Examples ##
If the extended class name is Sample_Admin_Pages, defining the following class method will embed a banner image in all pages created by the class.

	class Sample_Admin_Pages extends AdminPageFramework {
	...
		function head_Sample_Admin_Pages( $strContent ) {
			return '<div style="float:right;"><img src="' . plugins_url( 'img/banner468x60.gif', __FILE__ ) . '" /></div>' 
				. $strContent;
		}
	...
	}
	
If the created page slug is my_first_setting_page, defining the following class method will filter the middle part of the page output.

	class Sample_Admin_Pages extends AdminPageFramework {
	...
		function content_my_first_setting_page( $strContent ) {
			return $strContent . '<p>Hello world!</p>';
		}
	...
	}
	
## Timing of Hooks ##

	------ When the class is instantiated ------

	start_ + extended class name

	------ Start Rendering HTML ------

	<head>
		<style type="text/css" name="admin-page-framework">
			style_ + page slug + _ + tab slug
			style_ + page slug
			style_ + extended class name
			script_ + page slug + _ + tab slug
			script_ + page slug
			script_ + extended class name		
		</style>
		
	</head>

	do_before_ + extended class name
	do_before_ + page slug
	do_before_ + page slug + _ + tab slug

	<div class="wrap">

		head_ + page slug + _ + tab slug
		head_ + page slug
		head_ + extended class name					
			
		<div class="acmin-page-framework-container">
			<form action="options.php" method="post">
			
				do_form_ + page slug + _ + tab slug
				do_form_ + page slug
				do_form_ + extended class name

				extended class name + _ + section_ + section ID
				extended class name + _ + field_ + field ID
				
				content_ + page slug + _ + tab slug
				content_ + page slug
				content_ + extended class name

				do_ + extended class name					
				do_ + page slug
				do_ + page slug + _ + tab slug

			</form>					
		</div>

			foot_ + page slug + _ + tab slug
			foot_ + page slug
			foot_ + extended class name			

	</div>

	do_after_ + extended class name
	do_after_ + page slug
	do_after_ + page slug + _ + tab slug


	----- After Submitting the Form ------

	validation_ + page slug + _ + tab slug 
	validation_ + page slug 
	export_ + page slug + _ + tab slug 
	export_ + page slug 
	export_ + extended class name
	import_ + page slug + _ + tab slug
	import_ + page slug
	import_ + extended class name
