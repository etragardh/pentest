<?php

/**
 *
 * @psalm-type DynamicFieldReturnType = string
 * @psalm-type DynamicField = array{
 * category:string,
 * slug:string,
 * label:string,
 * visibility:string,
 * controls:Control[],
 * returnTypes:DynamicFieldReturnType[],
 * handler:Closure(mixed):mixed
 * }
 *
 * @psalm-type DynamicPropertyPath = array{path:string,accepts:DynamicFieldReturnType}
 * @psalm-type DynamicDropdown = array{text: string, value: string}
 * @psalm-type ACFRule = array{param: string, operator: string, value: string}
 * @psalm-type ACFLocation = ACFRule[]
 * @psalm-type ACFGroup = array{ID: integer, location: ACFLocation[], title: string}
 * @psalm-type ACFFieldProperties = array{
 *  ID: integer,
 *  key: string,
 *  prefix: string,
 *  label: string,
 *  group_id: integer,
 *  group: string,
 *  name: string,
 *  type: string,
 *  value: string,
 *  is_option_page: boolean,
 *  parent_type?: "repeater" | "group",
 *  parent_key?: string,
 *  parent_repeater?: integer,
 * }
 * @psalm-type ACFField = ACFFieldProperties & array{
 *  sub_fields?: ACFFieldProperties[]
 * }
 * @psalm-type ACFFieldObject = object{
 *  field: ACFField
 * }
 * @psalm-type MetaboxField = array{
 *  id: integer,
 *  group_id: string,
 *  group: string,
 *  name: string,
 *  type: string,
 *  value: string,
 *  is_sub_field: boolean,
 *  is_settings_page: boolean,
 *  settings_page: string
 * }
 * @psalm-type MetaboxGroup = object{
 *   title: string,
 *   id: string,
 *   fields: MetaboxField[],
 *   settings_pages?:string|string[]|false
 * }
 * @psalm-type MetaboxRegistry = object{
 *  all:callable():MetaboxGroup[]
 * }
 * @psalm-type MetaboxSettingsPage = object{
 *  id: string,
 *  option_name: string,
 *  page_title: string,
 * }
 * @psalm-type ToolsetField = array{
 *   slug: string,
 *   name: string,
 *   group: string,
 *   post_types: string[],
 *   is_sub_field: boolean
 * }
 */
