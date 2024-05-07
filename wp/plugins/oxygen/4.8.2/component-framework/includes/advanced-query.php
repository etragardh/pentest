<?php

class Oxy_VSB_Advanced_Query {

    static function if_shortcode_process($val) {
            
        if(strpos($val, '[oxygen') !== false) {
            return do_shortcode($val);
        }

        return $val;
    }

    static function process_adv_query_args($children) {
            
        $args = array();

        $booleans = array(
            'include_children',
            'has_password',
            'nopaging',
            'ignore_sticky_posts',
            'inclusive',
            'cache_results',
            'update_post_term_cache',
            'update_post_meta_cache',
            'no_found_rows',
            'exact',
            'sentence'
        );
        
        global $ct_for_builder;

        foreach($children as $child) {
            $exploded = false;
            $result = null;
        
            $key = isset($child['key']) && !empty($child['key']) ? $child['key'] : null;

            if(isset($child['values']) && is_array($child['values']) && sizeof($child['values']) > 0) {
                $result = self::process_adv_query_args($child['values']);

                if(strpos($key, '__') === false && is_array($result) && sizeof($result) === 1) {
                    foreach($result as $k => $value) {
                        if(is_numeric($k) && !intval($k)) {
                            $result = $value;
                        }
                    }
                }
            }
            elseif(isset($child['value'])) {
                if(!oxygen_doing_oxygen_elements() && !$ct_for_builder && in_array($key, array('compare', 'operator'))) {
                  $child['value'] = base64_decode($child['value']);
                }
                $result = self::if_shortcode_process($child['value']);
                if($key && array_search($key, $booleans) !== false) {
                    $result = filter_var($result, FILTER_VALIDATE_BOOLEAN);
                } elseif(strpos($result, ',') !== false) {
                  // its a comma separated string, convert it into an array
                    $result = array_map('trim', explode(',', $result));
                    $exploded = true;
                }
            }

            if($exploded) {
              $args = $result;
            } else {
              if($key && $key == 'array') {
                  $args[] = array($result);
              }
              else if($key) {
                  $args[$key] = $result;
              } else {
                  $args[] = $result;
              }
            }
        }

        return $args;
    }

	static function query_args($params) {

	
        $args = self::process_adv_query_args($params);

        // pagination
        if (get_query_var('paged') && (!isset($args['nopaging']) || !$args['nopaging'])) {
            $args['paged'] = get_query_var( 'paged' );
        }
        
        // fail safe, for esssential params
        if(!isset($args['post_type'])) {
            $args['post_type'] = 'any';
        }

        return $args;
	}

  static function controls($tag = 'oxy_dynamic_list') {
     
    //  below is logic to convert settings to a preset
    // var component = iframeScope.findComponentItem(iframeScope.componentsTree.children, iframeScope.component.active.id, iframeScope.getComponentItem);
    // var proc = JSON.parse(JSON.stringify(component.options['original']["wp_query_advanced"]))
    // proc.forEach( item => { if(item['$$hashKey']) delete(item['$$hashKey']) })
    // console.log(JSON.stringify(proc).replace(/'/g, "\\'").replace(/"/g, "'"))
    
    $presets = array(
          array(
              'title' => 'Posts of the same post type as the current',
              'value' => "[{key:'post_type','values':[{'value':'[oxygen data=\'post_type\']'}]}]"
          ),
          array(
              'title' => 'All posts except the current',
              'value' => "[{'key':'post__not_in','values':[{'value':'[oxygen data=\'id\']'}]}]"
          ),
          array(
              'title' => 'Children of current post',
              'value' => "[{'key':'post_parent','values':[{'value':'[oxygen data=\'id\']'}]}]"
          ),
          array(
              'title' => 'Related posts by terms',
              'params' => "[{name: 'taxonomy', values: CtBuilderAjax.taxonomies}]",
              'value' => "[{'key':'post_type','values':[{'value':'[oxygen data=\'post_type\' ]'}]},{'key':'tax_query','values':[{'value':'','values':[{'value':'{{taxonomy}}','values':null,'key':'taxonomy'},{'value':'','values':[{'value':'[oxygen data=\'post_terms\' taxonomy=\'{{taxonomy}}\' ]'}],'key':'terms'},{'value':'slug','values':null,'key':'field'}],'key':'array'}]},{'key':'post__not_in','values':[{'value':'[oxygen data=\'id\' ]'}]}]"
          ),
          array(
            'title' => 'Random related posts by terms',
            'params' => "[{name: 'taxonomy', values: CtBuilderAjax.taxonomies}]",
            'value' => "[{'key':'post_type','values':[{'value':'[oxygen data=\'post_type\' ]'}]},{'key':'tax_query','values':[{'value':'','values':[{'value':'{{taxonomy}}','values':null,'key':'taxonomy'},{'value':'','values':[{'value':'[oxygen data=\'post_terms\' taxonomy=\'{{taxonomy}}\' ]'}],'key':'terms'},{'value':'slug','values':null,'key':'field'}],'key':'array'}]},{'key':'post__not_in','values':[{'value':'[oxygen data=\'id\' ]'}]},{'key':'orderby','values':[{'value':'rand'}]}]"
          ),
          array(
              'title' => 'Posts by author of current post or archive',
              'value' => "[{'key':'author','values':[{'value':'[oxygen data=\'phpfunction\' function=\'get_the_author_meta\' arguments=\'ID\' ]'}]}]"
          )

      )
    ?>
    <div
      ng-init="
      preset_query_params = [
      <?php
        foreach($presets as $preset) {
          echo "{ title: '".$preset['title']."', value: ".$preset['value'].(isset($preset['params'])?", params: ".$preset['params']:"")."},";
        }
      ?>
      ];
      "
      ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query']=='advanced' && iframeScope.component.options[iframeScope.component.active.id]['model']['use_acf_repeater']!='true' && iframeScope.component.options[iframeScope.component.active.id]['model']['use_metabox_clonable_group']!='true' "
    >
      <div class="oxygen-control-row">
          <div class="oxygen-control-wrapper">
              <label class="oxygen-control-label">Use a Preset</label>
              <div class="oxygen-select oxygen-select-box-wrapper">
                  <div class="oxygen-select-box">
                      <div class="oxygen-select-box-current">{{ iframeScope.component.options[iframeScope.component.active.id].model['wp_query_advanced_preset'] }}</div>
                      <div class="oxygen-select-box-dropdown"></div>
                  </div>
                  <div class="oxygen-select-box-options">
                      <div ng-repeat="(key, val) in preset_query_params" 
                          class="oxygen-select-box-option" ng-click="getDynamicQueryPreset(val.value, val.params, val.title);">
                          {{val.title}}
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="oxygen-control-row">
          <div class="oxygen-control-wrapper">
              <a href="#" class="oxygen-gradient-add-color" ng-click="showDialogWindow(); dialogForms['advancedquery'] = true; ">Edit Query</a>
          </div>
      </div>
    </div>
    <?php
  }

	static function dialog($tag = 'oxy_dynamic_list') {
		?>
		<script type="text/ng-template" id="ct-advanced-query-sub-value">
            <div class='oxygen-control-row'>

                <div ng-if="template" class="oxygen-control-wrapper" ng-class="{'key': !(subvalue.key && template[subvalue.key])}" ng-repeat="value in [subvalue]" ng-include="'ct-advanced-query-dropdown'"></div>
                
                <div ng-if="subvalue.key && template[subvalue.key]" class="oxygen-control-wrapper">
                    <div class="oxygen-gradient-add-color" ng-click="subvalue.values.push({value: ''}); iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')">Add Value</div>
                </div>
                
                <div ng-if="!(subvalue.key && template[subvalue.key])" class="oxygen-control-wrapper value">
                    <div class="oxygen-control">
                        <div class="oxygen-file-input">
                            <input type="text" spellcheck="false" ng-change="iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')" ng-model="subvalue.value">
                            <div class="oxygen-dynamic-data-browse" ctdynamicdata="" data="iframeScope.dynamicShortcodesContentMode" result="subvalue.value" callback="iframeScope.updateDynamicQueryParams">data</div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-wrapper delete">
                    <span ng-click="deleteDynamicQueryChild(children, $event, '.ct-advanced-query-value'); iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')">

                      <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg" title="Remove" />

                    </span>
                </div>
            </div>

             <div ng-show="subvalue.values && subvalue.values.length > 0" class="ct-dynamic-query-nested" >
                <div class="ct-advanced-query-value" ng-repeat="(deepindex, deepvalue) in subvalue.values track by deepindex">
                    <div class='oxygen-control-row'>
                        <div class="oxygen-control-wrapper value">
                            <div class="oxygen-control">
                                <div class="oxygen-file-input">
                                    <input type="text" spellcheck="false" ng-change="iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')" ng-model="deepvalue.value">
                                    <div class="oxygen-dynamic-data-browse" ctdynamicdata="" data="iframeScope.dynamicShortcodesContentMode" result="deepvalue.value" callback="iframeScope.updateDynamicQueryParams">data</div>
                                </div>
                            </div>
                        </div>

                        <div class="oxygen-control-wrapper delete">
                            <span ng-click="deleteDynamicQueryChild(subvalue.values, $event, '.ct-advanced-query-value'); iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')">
                              <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg" title="Remove" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
        </script>

        <script type="text/ng-template" id="ct-advanced-query-value">
            <div class='oxygen-control-row'>
                <div ng-if="template" class="oxygen-control-wrapper" ng-class="{'key': !(value.key && template[value.key])}" ng-include="'ct-advanced-query-dropdown'"></div>
                
                <div ng-if="value.key && template[value.key]" class="oxygen-control-wrapper">
                    <div class="oxygen-gradient-add-color" ng-click="value.values.push({value: ''}); iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')">Add Value</div>
                </div>
                
                <div ng-if="!(value.key && template[value.key])" class="oxygen-control-wrapper value">
                    <div class="oxygen-control">
                        <div class="oxygen-file-input">
                            <input type="text" spellcheck="false" ng-change="iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')" ng-model="value.value">
                            <div class="oxygen-dynamic-data-browse" ctdynamicdata="" data="iframeScope.dynamicShortcodesContentMode" result="value.value" callback="iframeScope.updateDynamicQueryParams">data</div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-wrapper delete">
                    <span ng-click="deleteDynamicQueryChild(children, $event, '.ct-advanced-query-value'); iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')">
                      <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg" title="Remove" />
                    </span>
                </div>
            </div>
            
            <div ng-show="value.values && value.values.length > 0" class="ct-dynamic-query-nested" >
                <div class="ct-advanced-query-value" ng-include="'ct-advanced-query-sub-value'" ng-init="template=template[value.key]; children=value.values" ng-repeat="(subindex, subvalue) in value.values track by subindex">

                </div>
            </div>
        </script>
        
        <script type="text/ng-template" id="ct-advanced-query-dropdown">
            <div class='oxygen-control'>
                <select
                    class="oxy-advanced-query-param-dropdown"
                    ng-init="initSelect2('oxy-advanced-query-param-dropdown', 'Choose Option', 'class')"
                    ng-model="value.key"
                    ng-change="advancedQuerySelect2Change(value.key, '{{value.key}}', value)"
                    ng-model-options="{ debounce: 10 }">
                    <option 
                        ng-repeat="(key, struct) in template"
                        ng-selected="value.key==key"
                        value="{{key}}">
                        {{key?key:'&nbsp;'}}
                    </option>
                </select>
            </div>
        </script>
        <script type="text/ng-template" id="ct-advanced-query-param">
            <div class='oxygen-control-row'>
                <div class='oxygen-control-wrapper'>
                    <div class='oxygen-control'>
                        <select
                            class="oxy-advanced-query-param-dropdown"
                            ng-init="initSelect2('oxy-advanced-query-param-dropdown', 'Choose parameter...', 'class')"
                            ng-model="param.key"
                            ng-change="advancedQuerySelect2Change(param.key, '{{param.key}}', param)"
                            ng-model-options="{ debounce: 10 }">
                            <optgroup 
                                ng-repeat="(type, keys) in all_query_params"
                                label="{{type}}">
                                <option 
                                    value="{{key}}"
                                    ng-selected="param.key==key"
                                    ng-repeat="key in keys">
                                    {{key}}
                                </option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="oxygen-control-wrapper">
                    <div ng-if="param.key && (param.values.length < 1 || param.key.indexOf('__')!==-1 || array_query_params.indexOf(param.key) !== -1 || templates_query_params.hasOwnProperty(param.key))" class="oxygen-gradient-add-color" ng-click="param.values.push({value: ''}); iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')">Add Value</div>
                </div>

                <div class="oxygen-control-wrapper delete">
                    <span ng-click="deleteDynamicQueryChild(children, $event, '.ct-advanced-query-param'); iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')">
                      <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg" title="Remove" />
                    </span>
                </div>
            </div>
            <div ng-show="param.values.length > 0">
                <div class="ct-advanced-query-value" ng-include="'ct-advanced-query-value'" ng-init="children = param.values; template = templates_query_params[param.key]" ng-repeat="(vindex, value) in param.values track by vindex">

                </div>
            </div>
        </script>

        <script type="text/ng-template" id="ct-advanced-query">
          
            <div class="ct-advanced-query">
                <div ng-include="'ct-advanced-query-param'" class="ct-advanced-query-param" ng-init="children = iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced']" ng-repeat="(index, param) in iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced']"></div>
            </div>
          
        </script>
        <div ng-class="{'ct-advanced-query-scroll': iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced'] && iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced'].length}">
          <div 
              ng-include="'ct-advanced-query'" 
              ng-init="
              array_query_params = [
                  'post_type',
                  'post_status'
              ];
              templates_query_params = {
                  comment_count: {
                      '': null,
                      value: null,
                      compare: null
                  },
                  date_query: {
                      'year': null,
                      'month': null,
                      'week': null,
                      'day': null,
                      'hour': null,
                      'minute': null,
                      'second': null,
                      'after': {
                          '': null,
                          'year': null,
                          'month': null,
                          'day': null
                      },
                      'before': {
                          '': null,
                          'year': null,
                          'month': null,
                          'day': null
                      },
                      'inclusive': null,
                      'compare': null,
                      'column': null,
                      'relation': null
                  },
                  tax_query: {
                      'relation': null,
                      'array': {
                          'taxonomy': null,
                          'field': null,
                          'terms': 'array',
                          'include_children': null,
                          'operator': null
                      }
                  },
                  meta_query: {
                      'relation': null,
                      'array': {
                          'key': null,
                          'value': 'array',
                          'type': null,
                          'compare': null,
                      }
                  }
              };
              all_query_params = {
                  'Author': [
                      'author',
                      'author__name',
                      'author__in',
                      'author__not_in',
                  ],
                  'Category': [
                      'cat',
                      'category_name',
                      'category__and',
                      'category__in',
                      'category__not_in'
                  ],
                  'Tag': [
                      'tag',
                      'tag_id',
                      'tag__and',
                      'tag__in',
                      'tag__not_in',
                      'tag_slug__and',
                      'tag_slug__in'
                  ],
                  'Taxonomy': [
                      'tax_query'
                  ],
                  'Post & Page': [
                      'p',
                      'name',
                      'title',
                      'page_id',
                      'pagename',
                      'post_name__in',
                      'post_parent',
                      'post_parent__in',
                      'post_parent__not_in',
                      'post__in',
                      'post__not_in',
                      'post_type',
                      'post_status',
                      'fields'
                  ],
                  'Comments': [
                      'comment_count'
                  ],
                  'Password': [
                      'has_password',
                      'post_password'
                  ],
                  'Date & Time': [
                      'year',
                      'monthnum',
                      'w',
                      'day',
                      'hour',
                      'minute',
                      'second',
                      'm',
                      'date_query'
                  ],
                  'Meta': [
                      'meta_key',
                      'meta_value',
                      'meta_value_num',
                      'meta_compare',
                      'meta_query'
                  ],
                  'Pagination': [
                      'posts_per_page',
                      'nopaging',
                      'paged',
                      'posts_per_archive_page',
                      'offset',
                      'page',
                      'ignore_sticky_posts'
                  ],
                  'Order': [
                      'order',
                      'orderby'
                  ],
                  'Permissions': [
                      'perm'
                  ],
                  'Mime Type': [
                      'post_mime_type'
                  ],
                  'Caching': [
                      'cache_results',
                      'update_post_term_cache',
                      'update_post_meta_cache',
                      'no_found_rows'
                  ],
                  'Search': [
                      's',
                      'exact',
                      'sentence'
                  ]
              }
              ">
              
          </div>
        </div>
        <div class='oxygen-condition-builder-add-condition' 
            ng-if="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced'] && iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced'].length">
            
            <a ng-click="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced'].push({key:null, values:[]}); iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')"><?php _e("Add Parameter","oxygen");?></a>
        </div> 

        <div class="oxygen-add-button"
            ng-if="!iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced'] || !iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced'].length"
            ng-click="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query_advanced'].push({key:null, values:[]}); iframeScope.setOption(iframeScope.component.active.id, '<?php echo $tag; ?>', 'wp_query_advanced')">
            
            <span><?php _e("Add Query Parameter","oxygen");?></span>
        </div>

		<?php
	}
}