function ovsbCSS() {
    return {
        currentTheme: 'default',
        fullScreen: false,
        stylesheets: window.stylesheets,
        selectedSheet: 0,
        content: null,
        hotReloadEnabled: window.hotReloadStatus,
        sheetsSavedNotice: "hide",
        sheetsSavingNotice: "hide",
        hotReloadWait: false,
        lockStylesheetsInBuilder: "",
        lockStylesheetsInBuilderWait: false,
        settingsPopup: false,
        sheetTemplate: {
            css: "",
            id: 0,
            name: "ovsbCSS-stylesheet-mgmt-sheet-name",
            parent: 0
        },
        setupCodemirror() {
		    codeMirrorEditor = new OxyCM.EditorView({
                state: OxyCM.EditorState.create({
                    extensions: [
                        OxyCM.basicSetup,
                        OxyCM.modules.keymap.of([OxyCM.modules.indentWithTab]),
                        OxyCM.modules.search(),
                        OxyCM.modules.css(),
                        OxyCM.EditorView.lineWrapping,
                        OxyCM.modules[window.globalCodeMirrorTheme],
                        OxyCM.EditorView.updateListener.of((v)=> {
                            if(v.docChanged) {
                                let thisSheet = this.getSheetById(this.selectedSheet);//this.stylesheets[this.selectedSheet];

                                if( !thisSheet ) return;

                                thisSheet.css = this.utf8_to_b64( codeMirrorEditor.state.doc.toString() );
                                thisSheet.modified = true;
                            }
                        }),
                    ],
                    doc: 'Click a stylesheet to begin editing.'
                }),
                parent: document.getElementById("ovsb-css-codemirror")
            })
        },
        updateCodemirror(content) {
            let transaction = codeMirrorEditor.state.update({changes: {from: 0, to: codeMirrorEditor.state.doc.length, insert: content}})
            codeMirrorEditor.dispatch(transaction)
        },
        setupShortcuts() {
            jQuery('body').on('keydown', 
                    {_this: this}, // pass the ovsbCSS() instance to event.data to access from callback
                    this.cancelDefaults);
        },
        cancelDefaults(event) {
            var keyboardShortcuts = [
                's',
            ]
        
            if( !keyboardShortcuts.includes(event.key.toLowerCase()) ) {
                return;
            }
        
            if( event.ctrlKey || event.metaKey ) {
                if (event.data._this !== undefined) {
                    let keyDownCallback = event.data._this.debounce(event.data._this.shortcutHandler, 250)
                    keyDownCallback(event);
                    return false;
                }
            }
        },
        debounce(callback, delay) {
            var timeout;
            return function () {
                var context = this;
                var args = arguments;
                if (timeout) {
                    clearTimeout(timeout);
                }
                timeout = setTimeout(function () {
                    timeout = null;
                    callback.apply(context, args);
                }, delay);
            }
        },
        shortcutHandler(event) {
            // Stop event processing if it is repeating
            if (event.originalEvent.repeat) {
                return;
            }
        
            // Process the shortcut events
            var processed = false;
            var key = event.key.toLowerCase();
        
            if( (event.ctrlKey || event.metaKey) && !event.shiftKey ) {
                switch (key) {
                    case 's':
                        event.data._this.saveSheets();
                        processed = true;
                        break;
                } 
            }
        
            // If the shortcut event was processed, stop the event propagation and cancel it
            if (processed) {
                return false;
            }
        },
        safeCopy( object ) {
            let objectCopy = JSON.parse( JSON.stringify( object ) );

            return objectCopy;
        },
        saveSheets() {
            let formData = new FormData();
            let ajaxResponse = '';
            let returnedData = null;
            let error = null;

            this.sheetsSavingNotice = "show"

            this.stylesheets = this.removeModifiedFlagFromSheets();

            formData.append( 'action', 'oxy_save_css_from_admin' );
            formData.append( 'stylesheets', JSON.stringify( this.stylesheets ) );

            fetch( ajaxurl, {
                method: 'POST',
                body: formData,
            })
            .then( response => {
                ajaxResponse = response.text() 
                this.sheetsSavingNotice = "hide"
                this.sheetsSavedNotice = "show"
                setTimeout( () => {
                    this.sheetsSavedNotice = "hide"
                }, 2900)
                return true;
            })
            .then( data => returnedData = data )
            .catch( error => { 
                this.sheetsSavingNotice = "hide"
                console.log( error ) 
                alert('Problem occurred while saving sheets.')
                return false;
            })

        },
        getSheetsButNotFolders() {
            return this.stylesheets.filter( obj => { return !obj.folder } );
        },
        getFolderName(id) {
            return this.stylesheets.find( obj => { return obj.id == id } )?.name;
        },
        getSheetById(id) {
            return this.stylesheets.find( obj => { return obj.id == id } );
        },
        getCurrentSheetCss() {
            return this.stylesheets.find( obj => { return obj.id == this.selectedSheet } ).css;
        },
        getSheetIndexById(id) {
            return this.stylesheets.findIndex( obj => { return obj.id == id } );
        },
        removeSheetById(id) {
            if( !id ) return false;

            let sheetsCopy = this.safeCopy( this.stylesheets );

            sheetsCopy = this.stylesheets.filter( obj => { return obj.id != id } );

            return sheetsCopy;
        },
        createNewSheet(name) {
            if( !name ) return this.stylesheets;

            if( !name.match(/^[a-z_-][a-z\d_-]*$/i) ) {
                alert('Stylesheet names can only contain letters and numbers.');
                return this.stylesheets;
            }

            let sheetsCopy = this.safeCopy( this.stylesheets );

            let newSheet = { ...this.sheetTemplate };

            newSheet.id = this.getNextStylesheetId();
            newSheet.name = name;

            sheetsCopy.push( newSheet );

            return sheetsCopy;
        },
        removeModifiedFlagFromSheets() {
            let sheetsCopy = this.safeCopy( this.stylesheets );

            sheetsCopy.forEach( (sheet) => {
                sheet.modified = false;
            })

            return sheetsCopy;
        },
        getNextStylesheetId() {
            return Math.max(...this.stylesheets.map(o => o.id), 0) + 1;
        },
        toggleHotReload() {
            this.hotReloadWait = true;
            let toggleHotReload = fetch(ajaxurl, {
                method: "POST",
                credentials: "same-origin",
                headers: new Headers({"Content-Type": "application/x-www-form-urlencoded"}),
                body: "action=oxy_css_toggle_hot_reload"
            })
            .then( response => { this.hotReloadWait = false; return response.text(); } )
            .then( result => this.hotReloadEnabled = window.hotReloadStatus = result );
        },
        toggleStylesheetsEditLock() {
            this.lockStylesheetsInBuilderWait = true;
            fetch(ajaxurl, {
                method: "POST",
                credentials: "same-origin",
                headers: new Headers({"Content-Type": "application/x-www-form-urlencoded"}),
                body: "action=oxy_css_toggle_stylesheet_edit_lock"
            })
            .then( response => { this.lockStylesheetsInBuilderWait = false; return response.text(); } )
            .then( result => { this.lockStylesheetsInBuilder = result; });
        },
        b64_to_utf8(str) {
            // improved atob() to support UTF8 chars 
            try {
                var returnVal = decodeURIComponent(escape(window.atob(str)))
            } catch (error) {
                returnVal = window.atob(str)
            }
            
            return returnVal
        },
        utf8_to_b64(str) {
            return window.btoa(unescape(encodeURIComponent(str)));
        }
    }
}