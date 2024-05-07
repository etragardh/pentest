/* global breakdanceConfig */
(function($) {
  const { isGutenberg } = breakdanceConfig;
  let unsubscribeFromGutenberg = null;

  window.breakdanceUtils = {
    getPostId() {
      return $("#post_ID").val();
    },

    getPostTitle() {
      if (isGutenberg) {
        return wp.data.select("core/editor").getEditedPostAttribute("title");
      }

      // Classic Editor
      return $("#title").val();
    },

    setPostTitle(title) {
      if (isGutenberg) {
        return wp.data.dispatch("core/editor").editPost({
          title
        });
      }

      // Classic Editor
      $("#title").val(title);
      $("#title-prompt-text").css("display", "none");
    },

    getBuilderLoaderUrl() {
      const url = window.breakdanceConfig.builderLoaderUrl;
      return url.replace("%%POSTID%%", this.getPostId());
    },

    autogenerateTitleIfNotSet() {
      const postId = this.getPostId();
      const postTitle = this.getPostTitle();

      if (!postTitle) {
        this.setPostTitle(`Breakdance - ${postId}`);
      }

      return this;
    },

    redirectToBuilder(newTab = false, builderUrl = false) {
      let url = this.getBuilderLoaderUrl();
      if (builderUrl) {
        url = builderUrl;
      }

      // https://stackoverflow.com/questions/28295813/wordpress-disable-are-you-sure-you-want-to-navigate-away-from-this-page-whe
      $(window).off("beforeunload.edit-post");

      if (newTab) {
        // Open in a new tab, or if popup is blocked, replace
        window.open(url, "_blank") || window.location.assign(url);
        return;
      }

      window.location = url;
    },

    saveGutenberg(callback) {
      wp.data.dispatch("core/editor").savePost();

      const { isSavingPost } = wp.data.select("core/editor");
      let checked = true;

      // WP core js doesn't have a good way to run some JS when the post is saved, but this works
      // https://github.com/WordPress/gutenberg/issues/17632#issuecomment-583772895
      wp.data.subscribe(() => {
        if (isSavingPost()) {
          checked = false;
        } else if (!checked) {
          callback();
          checked = true;
        }
      });
    },

    saveClassic(callback) {
      $("#title").trigger("blur");

      // https://wordpress.stackexchange.com/questions/236690/callback-for-wp-autosave-server-triggersave
      wp.autosave.server.triggerSave();

      $(document).on("heartbeat-tick.autosave", () =>
        setTimeout(() => {
          callback();
        }, 200)
      );
    },

    disableAndExtractContent(callback) {
      const confirmed = confirm(
        `Breakdance will be disabled for this post.
        Your content will be migrated to the WordPress editor, and your design will be backed up as a revision.`
      );

      if (!confirmed) {
        return;
      }

      const payload = {
        action: "breakdance_disable_and_maybe_extract",
        id: this.getPostId(),
        should_extract: true
      };
      const urlObject = new URL(window.breakdanceConfig.ajaxUrl);
      if (window.breakdanceConfig.ajaxNonce) {
        urlObject.searchParams.set(
          "_ajax_nonce",
          window.breakdanceConfig.ajaxNonce
        );
      }

      $.post(urlObject.toString(), payload).done(() => {
        $(window).off("beforeunload.edit-post");
        window.location = window.location; // reload the page
      });

      if (callback) {
        callback();
      }
    },

    isAuxClick(event) {
      return (
        event.button === 1 ||
        event.shiftKey ||
        event.ctrlKey ||
        event.metaKey ||
        event.keyCode === 13
      );
    },

    enableGutenbergReadOnlyModeIfLauncherIsPresent() {
      const getBlockList = () =>
        wp.data.select("core/block-editor").getBlocks();

      document.body.classList.add("is-breakdance-launcher-active");

      if (unsubscribeFromGutenberg) {
        unsubscribeFromGutenberg();
      }

      unsubscribeFromGutenberg = wp.data.subscribe(() => {
        const isBreakdanceLauncherPresent = getBlockList().every(
          block => block.name === "breakdance/block-breakdance-launcher"
        );

        if (!isBreakdanceLauncherPresent) {
          this.addLauncherToGutenberg(false);
        }
      });
    },

    addLauncherToGutenberg(save = true) {
      if (window.breakdanceConfig.mode !== "breakdance") {
        // this is not a breakdance post
        return;
      }

      const launcherBlock = wp.blocks.createBlock('breakdance/block-breakdance-launcher');
      // Bring back the launcher if the user remove it somehow.
      // Post content is removed entirely if the launcher is present
      wp.data.dispatch("core/block-editor").resetBlocks([launcherBlock]);

      if (save) {
        window.breakdanceUtils.saveGutenberg(() => {
          console.log('Breakdance Launcher is missing, adding it back.');
        });
      }
    },

    disableGutenbergReadOnlyMode() {
      document.body.classList.remove("is-breakdance-launcher-active");
      unsubscribeFromGutenberg();
    }
  };
})(jQuery);
