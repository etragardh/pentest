const { __: $0012f20ec2c961dc$var$__  } = wp.i18n;
const { compose: $0012f20ec2c961dc$var$compose  } = wp.compose;
const { withSelect: $0012f20ec2c961dc$var$withSelect , withDispatch: $0012f20ec2c961dc$var$withDispatch  } = wp.data;
const { PluginDocumentSettingPanel: $0012f20ec2c961dc$var$PluginDocumentSettingPanel  } = wp.editPost;
const { ToggleControl: $0012f20ec2c961dc$var$ToggleControl , TextControl: $0012f20ec2c961dc$var$TextControl , PanelRow: $0012f20ec2c961dc$var$PanelRow  } = wp.components;
const $0012f20ec2c961dc$var$Fields = ({ postType: postType , postMeta: postMeta , setPostMeta: setPostMeta  })=>{
    // If you add a new field here, don't forget to add it to the classic editor as well. (classic.php)
    return /*#__PURE__*/ React.createElement($0012f20ec2c961dc$var$PluginDocumentSettingPanel, {
        title: "Breakdance Design Library",
        icon: "share"
    }, /*#__PURE__*/ React.createElement($0012f20ec2c961dc$var$PanelRow, null, /*#__PURE__*/ React.createElement($0012f20ec2c961dc$var$ToggleControl, {
        label: "Hide in Design Set?",
        onChange: (value)=>setPostMeta({
                _breakdance_hide_in_design_set: value
            }),
        checked: postMeta._breakdance_hide_in_design_set
    })), /*#__PURE__*/ React.createElement($0012f20ec2c961dc$var$PanelRow, null, /*#__PURE__*/ React.createElement($0012f20ec2c961dc$var$TextControl, {
        label: "Tags",
        value: postMeta._breakdance_tags,
        onChange: (value)=>setPostMeta({
                _breakdance_tags: value
            })
    })));
};
var $0012f20ec2c961dc$export$2e2bcd8739ae039 = $0012f20ec2c961dc$var$compose([
    $0012f20ec2c961dc$var$withSelect((select)=>{
        return {
            postMeta: select("core/editor").getEditedPostAttribute("meta"),
            postType: select("core/editor").getCurrentPostType()
        };
    }),
    $0012f20ec2c961dc$var$withDispatch((dispatch)=>{
        return {
            setPostMeta (newMeta) {
                dispatch("core/editor").editPost({
                    meta: newMeta
                });
            }
        };
    })
])($0012f20ec2c961dc$var$Fields);


const { registerPlugin: $60bbd8e6907d6f02$var$registerPlugin  } = wp.plugins;
$60bbd8e6907d6f02$var$registerPlugin("breakdance-design-library-fields", {
    render () {
        return /*#__PURE__*/ React.createElement((0, $0012f20ec2c961dc$export$2e2bcd8739ae039), null);
    }
});


//# sourceMappingURL=editor.js.map
