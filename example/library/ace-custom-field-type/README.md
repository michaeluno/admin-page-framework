#AceCustomFieldType

An [Ace](http://ace.c9.io/) Custom Field Type for the [Admin Page Framework](https://github.com/michaeluno/admin-page-framework)

![AceCustomFieldType - 'gutter' => true](AceCustomFieldType.png)


##Adding AceCustomFieldType

```php

class My_Settings extends AdminPageFramework {

    public function start_My_Settings() {

        if (! class_exists('AceCustomFieldType'))
            include_once(dirname( __FILE__ ) . '/AceCustomFieldType/AceCustomFieldType.php');

        $sClassName = get_class( $this );

        new AceCustomFieldType( $sClassName );
    }

    public function setUp() {

		/* add section etc */

        $this->addSettingFields(
            array(  // Ace Custom Field
                'field_id'          => 'style_editor',
                'section_id'        => 'a_section',
                'title'             => __('Style Editor', 'l10n' ),
                'description'       => __('Type a text string here.', 'l10n' ),
                'type'              => 'ace',
                'default'           => '',
                //'repeatable'        => true,
                // The attributes below are the defaults, i.e. if you want theses you don't have to set them
                'attributes' =>  array(
                    'cols'          =>  60,
                    'rows'          =>  4,
                ),
                // The options below are the  defaults, i.e. if you want theses you don't have to set them
                'options'    => array(
					'language'      => 'css', // available languages https://github.com/ajaxorg/ace/tree/master/lib/ace/mode
					'theme'         => 'chrome', //available themes https://github.com/ajaxorg/ace/tree/master/lib/ace/theme
					'gutter'        => false,
					'readonly'      => false,
					'fontsize'      => 12,
        		)
            )
        );
    }
}
```


##cdnjs

AceCustomFieldType is [using cdnjs](https://github.com/soderlind/AceCustomFieldType/blob/master/AceCustomFieldType.php#L45):

```php
protected function getEnqueuingScripts() {
    return array(
        array( 'src'    => '//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js', 'dependencies'    => array( 'jquery' ) ),
        /**
         * If you'd like to use a local ace library:
         *
         * 1) Go to the same folder as this file
         * 2) Clone ace-builds: git clone https://github.com/ajaxorg/ace-builds.git
         * 3) Remove the line above this comment section
         * 4) Uncomment the line below this comment section
         */
        //array( 'src'    => dirname( __FILE__ ) . '/ace-builds/src-min-noconflict/ace.js', 'dependencies'    => array( 'jquery' ) ),
    );
}
```


##Changelog
* 1.1.0 Removed local ace-builds library. AceCustomFieldType is now using  http://cdnjs.com/libraries/ace/
* 1.0.0 Cleaned up files, the `ace-builds` folder is no longer a submodule and only contains the src-min-noconflict build. The missing feature, mentioned below, is added in Admin Page Framework 3.3.0 
* 0.0.4 Added support for `'type' => 'revealer'`. Note [a missing feature in revealer](https://github.com/michaeluno/admin-page-framework/issues/147) is preventing it from saving state 
* 0.0.3 Added support for `'repeatable' => true`
* 0.0.2 Keeping it simple, AceCustomFieldType is feature complete, i.e. you can change language, theme and fontsize, enable/disable gutter and make it readonly.
* 0.0.1 Initial working release, there's still a lot todo

