<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * A field type is a concept that defines a particular group of fields to have their own behaviour and functionality.
 *
 * There are variety of field types built-in such as `text`, `select`, `radio`, `size`, `checkbox`, `hidden` etc.
 * For example, using the `textarea` field type, the user sees a larger text input area with multiple lines compared to the `text` field type.
 * The `textarea` field type is more suitable for submitting long text.
 *
 * Those types are defined with a class which extends the common base class {@link AdminPageFramework_FieldType} that provides necessary functions to define a field type.
 * By extending the base class, you can define your own field type.
 *
 * @package     AdminPageFramework/Common/Form/FieldType
 * @since       3.3.0
 * @heading     Field Types
 */
abstract class AdminPageFramework_FieldType_Documentation {}
