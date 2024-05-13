<?php

/**
 * @psalm-type ControlOptions = mixed
 * @psalm-type Control = array{slug:string,label:string,options:ControlOptions,enableMediaQueries:boolean,enableHover:boolean,children:array}
 * @psalm-type BuilderElementControls = array{contentSections:Control[],designSections:Control[],settingsSections:Control[]}
 *
 *
 * @psalm-type PropertiesData = mixed
 * @psalm-type TreeNodeData = array{type:string,properties:PropertiesData}
 * @psalm-type TreeNode = array{id:int,data:TreeNodeData,children:array}
 * @psalm-type Tree = array{root:array{id:int,data:TreeNodeData,children:TreeNode[]}, _nextNodeId: int,  exportedLookupTable: array<int, TreeNode>}
 * no recursive types in Psalm :(
 */

/**
 * @psalm-type Breakpoint = array{id:string,label:string,defaultPreviewWidth:int|"100%",minWidth?:int,maxWidth?:int}
 */

/**
 * @psalm-type BuilderAction = array{script: string, dependencies?: string[]}
 * @psalm-type BuilderActions = array{onPropertyChange?: BuilderAction[], onMountedElement?: BuilderAction[], onAfterDeletedElement?: BuilderAction[], onMovedElement?: BuilderAction[], onBeforeDeletingElement?: BuilderAction[]}
 *
 * @psalm-type GlobalGeneratedCssFilePaths = array{globalSettingsCssFilePath?: string, globalSelectorsCssFilePath?: string, defaultCssForAllElementsFilePath?: string}
 *
 * @psalm-type PostGeneratedCssFilePaths = array{postCssFilePath?: string, postDefaultsCssFilePath?: string}
 *
 * @psalm-type ElementDependenciesAndConditions = array{scripts?:string[],inlineScripts?:string[],styles?:string[],inlineStyles?:string[],builderCondition?:string,frontendCondition?:string}
 * @psalm-type ElementDependencyWithoutConditions = array{scripts?:string[],inlineScripts?:string[],styles?:string[],inlineStyles?:string[],googleFonts?:string[]}
 *
 * @psalm-type ElementAttribute = array{name:string,template:string}|array{name:string,propertyPath:string}|array{name:string,rawValue:string}
 */

/**
 * @psalm-type DefaultCSS = array{slug:string,css:string}
 * @psalm-type CSSRule = string
 * @psalm-type RenderedNodes = array{html:string,defaultCss:DefaultCSS[],cssRules:CSSRule[],dependencies:ElementDependencyWithoutConditions}
 * @psalm-type PageAssets = array{dependencies: ElementDependencyWithoutConditions, globalGeneratedCssFilePaths: GlobalGeneratedCssFilePaths, postsGeneratedCssFilePaths: PostGeneratedCssFilePaths[]}
 *
 * // Post may contain other posts, this is why postsGeneratedCssFilePaths is an array
 * @psalm-type PostAssetsWithoutDependencies = array{postsGeneratedCssFilePaths: PostGeneratedCssFilePaths[]}
 * @psalm-type PostAssets = PostAssetsWithoutDependencies&array{dependencies: ElementDependencyWithoutConditions}
 *
 * @psalm-type SsrNode = array{html: string}&PostAssets
 *
 * @psalm-type ElementSettings = array{proOnly: boolean, dependsOnGlobalScripts?: boolean, requiredPlugins?: string[]}
 */

/**
 * @psalm-type CSSSelector = array{name:string,type:"class"|"custom",properties:PropertiesData}
 *
 * @psalm-type Preset = array{slug:string,section:Control,availableInElementStudio:boolean}
 */
