## Basic Five Steps ##

To create an option page with this framework, follow these steps.

### 1. Include the Library Class ###

	if ( !class_exists( 'AdminPageFramework' ) ) 
		include_once( '/class/admin-page-framework.php' );
		
### 2. Extend the Library Class ###

**AdminPageFramework** is the class name defined by this framework. The extended class name is up to your choice. As an example, here, *APF_CreateSettingPage* is used.

	class APF_CreateSettingPage extends AdminPageFramework {
	}
	
### 3. Define the setUp() Method ###

The *setUp()* method is a special method defined by this framework that is automatically loaded (with the the *wp_loaded* hook). So we just override the method in the extended class. All settings need to be set within this method.

	public function setUp() {
		$this->setRootMenuPage( 'Settings' );			// specifies to which parent menu to belong.
		$this->addSubMenuPage(
			'My First Setting Page',    // page and menu title
			'my_first_settings_page' 	// page slug
		); 		
	}
	
### 4. Define methods for Hooks ###

The method name is made up of `the hook prefix + the page slug`. Here, `do_` is used as an example. There are lots of available hooks supported by this framework.

	public function do_my_first_settings_page() {  // do_ + pageslug
		?>
		<h3>Say Something</h3>
		<p>This is my first admin page!</p>
		<?php
	}

For available action hooks and filters, please refer to [Hooks and Callbacks](?post_type=apf_posts&page=documentation&tab=hooks_and_callbacks).	

### 5. Instantiate the Class ###

	new APF_CreateSettingPage;
	
## Simple Example Plugin ##

	if ( !class_exists( 'AdminPageFramework' ) ) 
		include_once( '/class/admin-page-framework.php' );
		
	class APF_CreateSettingPage extends AdminPageFramework {
	
		public function setUp() {
			$this->setRootMenuPage( 'Settings' );			// specifies to which parent menu to belong.
			$this->addSubMenuPage(
				'My First Setting Page',    // page and menu title
				'my_first_settings_page' 	// page slug
			); 		
		}
	
		public function do_my_first_settings_page() {  // do_ + pageslug
			?>
			<h3>Say Something</h3>
			<p>This is my first admin page!</p>
			<?php
		}	
		
	}
	new APF_CreateSettingPage;
	
### Screenshot ###

![Admin Page Framework - Getting Started](https://lh3.googleusercontent.com/-tqu3Q-GIMHM/URsBT1y8I4I/AAAAAAAAAOU/P8nEBBcjukA/s600/screenshot_demo_00.jpg)