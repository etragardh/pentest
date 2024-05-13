<?php

// adding comments here because Psalm is retarded and crashes with a comment.
// TemplateTypePreviewCallbackReturnType: returns false if the preview does not apply. If false it will be ignored in the logic of condition

/**
 *
 * @psalm-type TemplateRuleGroup = TemplateRule[]
 *
 * @psalm-type TemplatePreviewableItem = array{label: string, type: string, url: string}
 *
 * @psalm-type TemplateTypeSlug = string
 *
 * @psalm-type TemplateTypePreviewCallbackReturnType = TemplatePreviewableItem[]|false
 *
 * @psalm-type WordPressMetaQuery = array{
 *  compare: mixed|string,
 *  key: string,
 *  value?: string[]|string,
 * }
 * @psalm-type WordPressTaxQuery = array{
 *  field?: string,
 *  operator?: string,
 *  taxonomy: string,
 *  terms: WP_Term[]|int[],string[]
 * }
 * @psalm-type WordPressDateQuery = array{
 *  before: string,
 *  after: string,
 * }
 *
 * @psalm-type WordPressQueryVars = array{
 *  post_type?: string,
 *  post_status?: string,
 *  orderby?: string,
 *  order?: string,
 *  offset?: integer,
 *  posts_per_page?: integer,
 *  date?: string,
 *  beforeDate?: string,
 *  afterDate?: string,
 *  ignore_sticky_posts?: boolean,
 *  meta_query?: WordPressMetaQuery[],
 *  tax_query?: array<array-key, WordPressTaxQuery|string>,
 *  date_query?: WordPressDateQuery[],
 *  comment_count?: array{value: int, compare: string},
 *  post__not_in?: int[],
 *  post__in?: int[],
 * }
 *
 * @psalm-type TemplateType = array{
 * slug: TemplateTypeSlug,
 * label: string,
 * postType?: string,
 * callback: Closure():boolean,
 * templatePreviewableItems: Closure():TemplateTypePreviewCallbackReturnType,
 * defaultPriority?: int
 * }
 *
 * If TemplateType:postType is provided, it is ultimately passed to the templatePreviewableItems
 * callback on a TemplateCondition. For some conditions, knowing the postType when getting
 * the previewable items is either necessary to get the previewable items
 *
 * @psalm-type TemplateTypeCategory = array{
 * label: string,
 * types: TemplateType[]
 * }
 * @psalm-type JSTemplateType = array{slug: string,defaultPriority?: int}
 *
 * @psalm-type JSTemplateTypeCategory = array{label: string,types: JSTemplateType[]}
 *
 * @psalm-type TemplateConditionValue = array{text:string,value:string}
 * @psalm-type ItemGroup = array{label: string,items:TemplateConditionValue[],availableForType?:false|string[]}
 *
 * @psalm-type ConditionValuesCallbackReturnType = false|string[]|TemplateConditionValue[]|ItemGroup[]
 *
 * @psalm-type TemplateCondition = array{
 *   supports: ("element_display"|"templating"|"query_builder")[],
 *   slug: string,
 *   label: string,
 *   category: string,
 *   operands: string[],
 *   valueInputType?: "dropdown" | "datepicker" | "timepicker" | "number",
 *   values: Closure():ConditionValuesCallbackReturnType,
 *   callback: Closure(string=,mixed=,string=):boolean,
 *   queryCallback?: Closure(WordPressQueryVars=,string=,mixed=):WordPressQueryVars,
 *   templatePreviewableItems: false|Closure(string,mixed,string):TemplatePreviewableItem[],
 *   availableForType: TemplateTypeSlug[],
 *   proOnly?: boolean,
 * }
 *
 * @psalm-type TemplateConditionWithValues = array{
 *   slug: string,
 *   label: string,
 *   operands: string[],
 *   values: ConditionValuesCallbackReturnType,
 *   callback: Closure(string,mixed):boolean,
 *   queryCallback?: Closure(WordPressQueryVars=,string=,mixed=):array,
 *   templatePreviewableItems: false|Closure(string,mixed,string):TemplatePreviewableItem[],
 *   availableForType: TemplateTypeSlug[],
 *   supports: ("element_display"|"templating"|"query_builder")[],
 * }
 *
 * @psalm-type PopupTriggers = array{
 *   text: string,
 *   value: string,
 *   proOnly?: bool
 * }
 *
 *
 * @psalm-type TemplateRule = array{
 * operand: string,
 * ruleCategorySlug?: string,
 * ruleSlug?: string,
 * ruleDynamic?: string,
 * value?: string|string[]
 * }
 *
 * @psalm-type TemplateRuleGroup = TemplateRule[]
 *
 * @psalm-type TriggerOptions = array{
 * delay?: int,
 * percent?: int,
 * selector?: string,
 * scrollType?: string,
 * clickType?: string,
 * limit?: int
 * }
 *
 * @psalm-type Trigger = array{
 * slug: string,
 * options?: TriggerOptions
 * }
 *
 * @psalm-type PopupOptions = array{
 * showOnLoadMilliseconds?: int,
 * showOnInactivityMilliseconds?: int,
 * scrollPercent?: int,
 * scrollSelector?: string,
 * scrollLimit?: int,
 * exitIntentDelay?: int,
 * exitIntentLimit?: int,
 * clickSelector?: string,
 * clickLimit?: int,
 * onlyShowOnce?: boolean,
 * avoidMultiple?: boolean
 * }
 *
 * @psalm-type TemplateSettings = null|array{
 * parentId?: int,
 * type?: TemplateTypeSlug,
 * ruleGroups?: TemplateRuleGroup[],
 * triggers?: Trigger[],
 * priority?: int,
 * fallback?: boolean
 * }
 *
 * @psalm-type Template = array{
 * id: int,
 * settings: TemplateSettings
 * }
 *
 * @psalm-type TemplateData = array{
 * id: int,
 * title: string,
 * postType: string,
 * settings: TemplateSettings,
 * editInBreakdanceLink: string,
 * status: "publish" | "trash"
 * }
 *
 * @psalm-type CustomQuery = array{
 * source: "post_types"|"related"|"acf_relationship",
 * includeByAuthor: boolean,
 * includeByTaxonomies: string[]|null,
 * postTypes: string[],
 * conditions: TemplateRuleGroup[],
 * orderBy: string,
 * order: string,
 * meta_key?: string,
 * date:string,
 * beforeDate:string|null,
 * afterDate: string|null,
 * ignoreStickyPosts: boolean,
 * ignoreCurrentPost: boolean,
 * offset: int|null,
 * postsPerPage: int|null,
 * totalPosts: int|null,
 * acfField: string,
 * metaboxField: string,
 * metaQuery?: array{relation: 'AND'|'OR', metaQueries: mixed[]}
 * }
 *
 * @psalm-type QueryControlParams = array{
 * active: "custom" | "text" | "php",
 * text: string,
 * php: string,
 * custom: CustomQuery
 * }
 *
 * @psalm-type GlobalBlock = array{label:string,id:int,tree:Tree|false, status?: "publish"|"trash"}
 *
 */
