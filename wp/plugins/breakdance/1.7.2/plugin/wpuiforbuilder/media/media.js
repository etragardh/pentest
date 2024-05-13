(function () {
  function getFileTypesFromString(typeStr) {
    if (!typeStr) return null;

    return typeStr
      .replace('document', 'application')
      .split(',');
  }

  function getMediaOptions() {
    const url = new URL(document.location);
    const params = url.searchParams;
    const multiple = !!params.get('multiple');
    const type = getFileTypesFromString(params.get('types'));
    const postId = params.get('post_id');

    return { multiple, type, postId };
  }

  function getMediaFrame() {
    const options = getMediaOptions();
    wp.media.view.settings.post.id = options.postId;

    const state = new wp.media.controller.Library({
      title: "Upload Media",
      library: wp.media.query({
        type: options.type
      }),
      multiple: options.multiple,
      date: false
    });

    return wp.media({
      frame: 'post',
      type: options.type,
      multiple: options.multiple,

      button: {
        text: "Choose",
      },

      state: 'gallery',

      states: [state]
    });
  }

  function removeExtraTabs() {
    const tabsToRemove = [
      '#menu-item-insert',
      '#menu-item-gallery',
      '#menu-item-playlist',
      '#menu-item-video-playlist'
    ];

    tabsToRemove.forEach((tab) => {
      const node = document.querySelector(tab);
      node.style.display = 'none';
    });
  }

  function prettifyMediaLibrary(frame) {
    removeExtraTabs();

    // Navigate to main tab
    frame.el.querySelector('#menu-item-library').click();
  }

  // Event Dispatcher

  function dispatch(event) {
    return window.parent.document.dispatchEvent(event);
  }

  function createEvent(name, detail) {
    return new CustomEvent(name, {
      detail: JSON.parse(JSON.stringify(detail)),
    });
  }

  // Formatters

  function formatExternalImage(state) {
    return {
      id: -1,
      type: 'external_image',
      url: state.props.get('url'),
      alt: state.props.get('alt') || "",
      caption: state.props.get('caption') || "",
    }
  }

  function formatMedia(state) {
    const selection = state.get("selection");
    return selection.toJSON();
  }

  function formatAttachmentObject(state) {
    if (state.get('id') === 'embed') {
      return formatExternalImage(state);
    }

    return formatMedia(state);
  }

  function onMediaSelected(frame) {
    return () => {
      const state = frame.state();
      const attachments = formatAttachmentObject(state);
      const event = createEvent("breakdanceMediaChooserSelect", attachments);
      dispatch(event);
      console.debug('Media selected', attachments);
    }
  }

  function onMediaClosed(frame) {
    return () => {
      dispatch(new Event("breakdanceMediaChooserClose"));
    }
  }

  function onReady() {
    const frame = getMediaFrame();

    frame.on("insert select", onMediaSelected(frame));
    frame.on("close", onMediaClosed(frame));

    frame.open();

    prettifyMediaLibrary(frame);
    // Pre-select image when opening the media library.
    // const attachment = wp.media.attachment(id).fetch();
    // selection.add(attachment ? [attachment] : []);

    dispatch(new Event("breakdanceMediaChooserReady"));
  }

  jQuery(document).ready(onReady);
}());
