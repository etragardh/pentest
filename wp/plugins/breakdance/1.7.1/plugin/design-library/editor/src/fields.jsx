const {__} = wp.i18n;
const {compose} = wp.compose;
const {withSelect, withDispatch} = wp.data;

const {PluginDocumentSettingPanel} = wp.editPost;
const {ToggleControl, TextControl, PanelRow} = wp.components;

const Fields = ({postType, postMeta, setPostMeta}) => {
  // If you add a new field here, don't forget to add it to the classic editor as well. (classic.php)
  return (
    <PluginDocumentSettingPanel title="Breakdance Design Library" icon="share">
      <PanelRow>
        <ToggleControl
          label="Hide in Design Set?"
          onChange={(value) => setPostMeta({_breakdance_hide_in_design_set: value})}
          checked={postMeta._breakdance_hide_in_design_set}
        />
      </PanelRow>
      <PanelRow>
        <TextControl
          label="Tags"
          value={postMeta._breakdance_tags}
          onChange={(value) => setPostMeta({_breakdance_tags: value})}
        />
      </PanelRow>
    </PluginDocumentSettingPanel>
  );
};

export default compose([
  withSelect((select) => {
    return {
      postMeta: select("core/editor").getEditedPostAttribute("meta"),
      postType: select("core/editor").getCurrentPostType(),
    };
  }),
  withDispatch((dispatch) => {
    return {
      setPostMeta(newMeta) {
        dispatch("core/editor").editPost({
          meta: newMeta
        });
      }
    };
  })
])(Fields);
