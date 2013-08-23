### When you include the framework, modify the class names ###
When you include the library file, *admin-page-framework.php*, it's recommended to rename all the class names used in the library. This is because if a lesser version of the framework is loaded earlier than the one you include, your program may cause unexpected behaviour. 

Let's think about a situation like the user uses Plugin A and Plugin B. Plugin A uses Admin Page Framework v2.0.0 and Plugin B uses v2.0.1 with a new method implemented. Then if WordPress loads Plugin A earlier than Plugin B, the Plugin B's code will break.

All the used class names have the prefix of ***AdminPageFramework_*** so just replace the string with something else of your choice. Most code editors supports the **Replace All** command. 

### Manipulate the framework's properties ###
In Admin Page Framework v2, most framework's internal data is stored in one location except the data for custom post types and meta boxes. The settings that determine the framework's behavior is stored in `$this->oProps`. All the form data is stored in `$this->oProps->arrOptions`. So if you feel tedious using the predefined methods, just edit the oProps object's properties directly. 

The property contents can be viewed this way:

	echo $this->oDebug->getArray( get_object_vars( $this->oProps ) ); 
	
***

#### Do you have any tips? ####
Let us know if you know good tips for Admin Page Framework that can be shared with others! Please submit an issue [here](https://github.com/michaeluno/admin-page-framework/issues).

