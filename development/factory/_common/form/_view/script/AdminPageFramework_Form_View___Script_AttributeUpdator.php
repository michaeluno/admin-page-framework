<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript scripts to update attribute values.
 * 
 * @since       3.0.0     
 * @since       3.3.0       Extends `AdminPageFramework_Form_View___Script_Base`.
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Form_View___Script_AttributeUpdator extends AdminPageFramework_Form_View___Script_Base {
    
    /**
     * Returns an inline JavaScript script.
     * 
     * @since       3.0.0   
     * @since       3.3.0       Changed the name from `getjQueryPlugin()`.
     * @param       $oMsg       object      The message object.
     * @return      string      The inline JavaScript script.
     */
    static public function getScript( /* $oMsg */ ) {
        
        // Uncomment these lines when parameters need to be accessed.
        // $_aParams   = func_get_args() + array( null );
        // $_oMsg      = $_aParams[ 0 ];            

        /**
         * Attribute increment/decrement jQuery Plugin
         */
        return <<<JAVASCRIPTS
(function ( $ ) {
       
    /**
     * Increments a digit in the given string by the model.
     * 
     * @since       3.6.0
     * @return      string
     */
    $.fn.incrementDigitByModel = function( sString, iIncrementFrom, sModel, sDigitKey ) {
        
        if ( 'string' !== typeof sString  ) {
            return sString;
        }
        
        var sDigitKey = sDigitKey ? sDigitKey : '___i___';
        
        // Escape regex characters. 
        sModel = sModel.replace( 
            /[-[\]{}()*+?.,\\^$|#\s]/g, // Use the g modifier to apply the changes to all the matches.
            "\\\\$&"  // in a test script this was okay with double-backslashes "\\$&"
        ); 

        // Construct a regex needle pattern.
        var _oRegex = new RegExp( '^(.+?)(' + sDigitKey + ')(.*?)$', 'g' );           
        sModel = sModel.replace( _oRegex, '($1)(\\\d+)($3.*?)' ); 

        var _oRegex = new RegExp( sModel );
        sString = sString.replace( _oRegex, function ( sFullMatch, sMatch0, sMatch1, sMatch2 ) {
            iIncrementFrom = 'undefined' === typeof iIncrementFrom 
                ? sMatch1
                : iIncrementFrom;

            return sMatch0 + ( Number( iIncrementFrom ) + 1 ) + sMatch2;
        } );  
        return sString;
    };

    /**
     * Increments a digit of the given attribute value.
     * @sinec       3.6.0
     */
    $.fn.incrementAttributes = function( aAttributeNames, iIncrementFrom, sModel, sDigitKey ) {
        var _oThis = $( this );
        $.each( aAttributeNames, function( iOuterIndex, sAttributeName ) {
            _oThis.incrementAttribute( sAttributeName, iIncrementFrom, sModel, sDigitKey );
        });        
    };    
    
    /**
     * Increments a digit of the given attribute value.
     * @sinec       3.6.0
     */
    $.fn.incrementAttribute = function( sAttributeName, iIncrementFrom, sModel, sDigitKey ) {     
        return this.attr( sAttributeName, function( iIndex, sValue ) {    
            return $( this ).incrementDigitByModel( sValue, iIncrementFrom, sModel, sDigitKey );
        }); 
    };

    
    /**
     * Increments a digit of the given occurrence(nth/-nth) with the prefix of underscore in a specified attribute value.
     * if the biOccurrence is false, the last found one will be replaced.
     * @deprecated  3.6.0
     */
    $.fn.incrementIDAttribute = function( sAttribute, biOccurrence ) {     
        return this.attr( sAttribute, function( iIndex, sValue ) {    
            return updateID( iIndex, sValue, 1, biOccurrence );
        }); 
    };
    /**
     * Increments a digit of the given occurrence(nth/-nth) enclosed in [] in a specified attribute value.
     * @deprecated  3.6.0
     */
    $.fn.incrementNameAttribute = function( sAttribute, biOccurrence ) {     
        return this.attr( sAttribute, function( iIndex, sValue ) {    
            return updateName( iIndex, sValue, 1, biOccurrence );
        }); 
    };

    /**
     * Decrements a digit of the given occurrence(nth/-nth) with the prefix of underscore in a specified attribute value.
     * @deprecated  3.6.0
     */
    $.fn.decrementIDAttribute = function( sAttribute, biOccurrence ) {
        return this.attr( sAttribute, function( iIndex, sValue ) {
            return updateID( iIndex, sValue, -1, biOccurrence );
        }); 
    };     
    /**
     * Decrements a first/last found digit enclosed in [] in a specified attribute value.
     * @deprecated  3.6.0
     */
    $.fn.decrementNameAttribute = function( sAttribute, biOccurrence ) {
        return this.attr( sAttribute, function( iIndex, sValue ) {
            return updateName( iIndex, sValue, -1, biOccurrence );
        }); 
    };     
    
    /**
     * Sets the current index to the ID attribute. Used for sortable fields. 
     * @deprecated  3.6.0
     * */
    $.fn.setIndexIDAttribute = function( sAttribute, iIndex, biOccurrence ){
        return this.attr( sAttribute, function( i, sValue ) {
            return updateID( iIndex, sValue, 0, biOccurrence );
        });
    };
    /** 
     * Sets the current index to the name attribute. Used for sortable fields. 
     * @deprecated  3.6.0
     */
    $.fn.setIndexNameAttribute = function( sAttribute, iIndex, biOccurrence ){
        return this.attr( sAttribute, function( i, sValue ) {
            return updateName( iIndex, sValue, 0, biOccurrence );
        });
    };     
    
    /* Local Function Literals */    
    /**
     * Sanitizes the occurrence parameter value for backward compatibility.
     * 
     * @since   3.1.7
     * @deprecated  3.6.0
     */
    var sanitizeOccurrence = function( biOccurrence ) {
        
        // If not defined, pass -1 for the last occurrence.
        if ( 'undefined' === typeof biOccurrence ) {
            return -1;
        }
        // If true, it used to mean the first occurrence.
        if ( true === biOccurrence ) {
            return 1;
        }
        // If false, it used to mean the last occurrence.
        if ( false === biOccurrence ) {
            return -1;
        }
        // 0 may have been used to mean false which meant the last occurrence.
        if ( 0 === biOccurrence ) {
            return -1;
        }
        // If it is an integer, that is good.
        if ( 'number' === typeof biOccurrence ) {
            return biOccurrence;
        }
        // Otherwise, the default value will be returned
        return -1;
        
    }
    /**
     * Returns the modified ID string based on the modification type.
     * 
     * @since  3.0.0
     * @since  3.1.7    Made it possible to specify the occurrence to change.
     * @param  integer  iIndex              The element index
     * @param  string   sID                 The ID to modify, the subject string haystack.
     * @param  integer  iIncrementType      1: increment, 2: decrement, 3: no change
     * @param  mixed    biOccurrence        One based index of occurrence to apply the change. 1 is the first occurrence. -1 is the first from the last.
     * @deprecated  3.6.0
     */ 
    var updateID = function( iIndex, sID, iIncrementType, biOccurrence ) {

        if ( 'undefined' === typeof sID ) { return sID; }
        
        var _iCurrentOccurrence = 1;
        var _oNeedle            = new RegExp( '(.+?)__(\\\d+)(?=([_-]|$))', 'g' ); // triple escape - not sure why but on a separate test script, double escape was working
        var _oMatch             = sID.match( _oNeedle );                
        var _iTotalMatch        = null !== _oMatch && _oMatch.hasOwnProperty( 'length' ) ? _oMatch.length : 0;
        if ( _iTotalMatch === 0 ) { return sID; }
        var _iOccurrence        = sanitizeOccurrence( biOccurrence );
        var _bIsBackwards       = _iOccurrence < 0;
        _iOccurrence = _bIsBackwards ? _iTotalMatch + 1 + _iOccurrence : _iOccurrence;
        return sID.replace( _oNeedle, function ( sFullMatch, sMatch0, sMatch1 ) {

            // If the iterated item is not at the specified occurrence, return the unmodified string.
            if ( _iCurrentOccurrence !== _iOccurrence ) {
                _iCurrentOccurrence++;
                return sFullMatch;
            }            
            
            // At this point, the iteration is at the specified occurrence.
            switch ( iIncrementType ) {
                case 1:
                    var _sResult = sMatch0 + '__' + ( Number( sMatch1 ) + 1 );
                    break;
                case -1:
                    var _sResult = sMatch0 + '__' + ( Number( sMatch1 ) - 1 );
                    break;
                default:
                    var _sResult = sMatch0 + '__' + ( iIndex );
                    break;
            }             
            _iCurrentOccurrence++;
            return _sResult;
            
        });     
        
    }
    /**
     * Returns the modified string for name attributes based on the modification type.
     * 
     * @since  3.0.0
     * @since  3.1.7    Made it possible to specify the occurrence to change.
     * @param  integer  iIndex              The element index
     * @param  string   sName               The name attribute value to modify, the subject string haystack.
     * @param  integer  iIncrementType      1: increment, 2: decrement, 3: no change
     * @param  mixed    biOccurrence        One based index of occurrence to apply the change. 1 is the first occurrence. -1 is the first from the last.
     * @deprecated  3.6.0
     */
    var updateName = function( iIndex, sName, iIncrementType, biOccurrence ) {

        if ( 'undefined' === typeof sName ) { return sName; }

        var _iCurrentOccurrence = 1;
        var _oNeedle            = new RegExp( '(.+?)\\\[(\\\d+)(?=\\\])', 'g' );    // triple escape - not sure why but on a separate test script, double escape was working
        var _oMatch             = sName.match( _oNeedle );
        var _iTotalMatch        = null !== _oMatch && _oMatch.hasOwnProperty( 'length' ) ? _oMatch.length : 0;
        if ( _iTotalMatch === 0 ) { return sName; }
        var _iOccurrence        = sanitizeOccurrence( biOccurrence );
        var _bIsBackwards       = _iOccurrence < 0;
        _iOccurrence = _bIsBackwards ? _iTotalMatch + 1 + _iOccurrence : _iOccurrence;    
        return sName.replace( _oNeedle, function ( sFullMatch, sMatch0, sMatch1 ) {
            
            
            // If the iterated item is not at the specified occurrence, return the unmodified string.
            if ( _iCurrentOccurrence !== _iOccurrence ) {
                _iCurrentOccurrence++;
                return sFullMatch;
            }            
            
            // At this point, the iteration is at the specified occurrence.        
            switch ( iIncrementType ) {
                case 1:
                    var _sResult = sMatch0 + '[' + ( Number( sMatch1 ) + 1 );
                    break;
                case -1:
                    var _sResult = sMatch0 + '[' + ( Number( sMatch1 ) - 1 );
                    break;
                default:
                    var _sResult = sMatch0 + '[' + ( iIndex );
                    break;
            }                    
            _iCurrentOccurrence++;
            return _sResult;
            
        });
    }
    
}( jQuery ));
JAVASCRIPTS;
        
    }

}
