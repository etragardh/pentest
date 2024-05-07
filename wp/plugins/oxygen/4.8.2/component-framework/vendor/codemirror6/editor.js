import {basicSetup}     from "codemirror"
import {EditorState, Compartment} from "@codemirror/state"
import {EditorView, keymap}     from "@codemirror/view"
import {search}         from "@codemirror/search"
import {indentWithTab}  from "@codemirror/commands"

import {oneDarkTheme}   from "@codemirror/theme-one-dark"
//https://github.com/craftzdog/cm6-themes
import {basicLight}  from 'cm6-theme-basic-light'
import {solarizedDark}  from 'cm6-theme-solarized-dark'
import {solarizedLight} from 'cm6-theme-solarized-light'
import {gruvboxDark}    from 'cm6-theme-gruvbox-dark'
import {gruvboxLight}   from 'cm6-theme-gruvbox-light'
import {materialDark}   from 'cm6-theme-material-dark'
import {nord}           from 'cm6-theme-nord'

import {html}           from "@codemirror/lang-html"
import {javascript}     from "@codemirror/lang-javascript"
import {css}            from "@codemirror/lang-css"
import {php}            from "@codemirror/lang-php"


window.OxyCM = {
    Compartment: Compartment,
    basicSetup: basicSetup,
    EditorState: EditorState, 
    EditorView: EditorView,    
    modules: {
        html: html,
        javascript: javascript,
        css: css,
        php: php,
        basicLight: basicLight,
        oneDarkTheme: oneDarkTheme,
        solarizedDark: solarizedDark,
        solarizedLight: solarizedLight,
        gruvboxDark: gruvboxDark,
        gruvboxLight: gruvboxLight,
        materialDark: materialDark,
        nord: nord,
        search: search,
        indentWithTab: indentWithTab,
        keymap: keymap
    },
}

/** 
 * MANUAL UPDATES after each build
 */
    
/**
 * 1. Updated Indent More function to move after the cursor part only
 * Should be updated in editor.bundle.js everytime after you do "npm run start"

const indentMore = ({ state, dispatch }) => {
    if (state.readOnly)
        return false;
     
     if (state.selection && state.selection.ranges && state.selection.ranges[0] ) {
         if (state.selection.ranges[0].from == state.selection.ranges[0].to ) {
             dispatch({
                 changes: {
                     from: state.selection.ranges[0].from,
                     to: state.selection.ranges[0].to,
                     insert: state.facet(indentUnit)
                 },
                 selection: {anchor: state.selection.ranges[0].from + state.facet(indentUnit).length}
             })
             return true;
         }
     }
    
     dispatch(state.update(changeBySelectedLine(state, (line, changes) => {
        changes.push({ from: line.from, insert: state.facet(indentUnit) });
    }), { userEvent: "input.indent" }));
    return true;
};


*/

/**
 * 2. Replace cssCompletionSource function with the above two functions
 */

/*
function oxyGetStylesheetsVariables(){
        if (typeof($scope) === "undefined") return [];
        var css = '', rules, u, result;
        let styleSheets = $scope.iframeScope.styleSheets,
        regex = /(\-\-(\-?([a-zA-Z_]|[^\x00-\x7F]|\\(?:[0-9a-fA-F]{1,6}(?: |\t|\n|\r\n|\r\f)?|(?:[^0-9a-fA-F\r\n\f])))))([a-zA-Z0-9_-]|[^\x00-\x7F]|\\(?:[0-9a-fA-F]{1,6}(?: |\t|\n|\r\n|\r\f)?|(?:[^0-9a-fA-F\r\n\f])))*:/g;
        var excWords = /:not|:before|:after|:focus|:is/gi;
        
        styleSheets.forEach((sheet) => {
            if ("css" in sheet) css += sheet.css;
        });
        css = css.replace(excWords, '');
        rules = css.replace(/\([^()]*\)/g, '');
        result = rules.match(regex); 
        u = [...new Set(result)];
        var arrayFiltered = u.map(el => el.slice(2, -1)
        );

        return arrayFiltered;
    }

    const cssCompletionSource = context => {
        let { state, pos } = context, node = syntaxTree(state).resolveInner(pos, -1);
        let isDash = node.type.isError && node.from == node.to - 1 && state.doc.sliceString(node.from, node.to) == "-";

        if (node.name == "PropertyName" ||
            (isDash || node.name == "TagName") && /^(Block|Styles)$/.test(node.resolve(node.to).name)){
                    return { from: node.from, options: properties(), validFor: identifier$1 };
            }
        if (typeof($scope) !== "undefined" && node.name == "ClassName") 
            return { from: node.from, options: $scope.iframeScope.objectToArrayObject($scope.iframeScope.classes).map(name => ({ type: "class", label: name.key })), validFor: identifier$1 };
        if (node.name == "ValueName")
            return { from: node.from, options: values, validFor: identifier$1 };
        if (node.name == "PseudoClassName"){
            if (typeof(currentCMEditor) !== "undefined" && currentCMEditor.dom.closest("#oxy-custom-css-cm6")) {
                if (state.sliceDoc(node.from, node.to).includes("--")){
                    return { from: node.from, options: oxyGetStylesheetsVariables().map(name => ({ type: "variable", label: "--" + name })), validFor: identifier$1 };
                }
                else return { from: node.from, options: values, validFor: identifier$1 };
            }
            else {
                return { from: node.from, options: pseudoClasses, validFor: identifier$1 };
            }
        }
            
        if (node.name == "VariableName" || (context.explicit || isDash) && isVarArg(node, state.doc)){
            return { from: node.from, options: oxyGetStylesheetsVariables().map(name => ({ type: "variable", label: "--" + name })), validFor: identifier$1 };
        }
            
        if (node.name == "TagName") {
            if (typeof(currentCMEditor) !== "undefined" && currentCMEditor.dom.closest("#oxy-custom-css-cm6")) {
                return { from: node.from, options: properties(), validFor: identifier$1 };
            }
            else {
                for (let { parent } = node; parent; parent = parent.parent)
                    if (parent.name == "Block")
                        return { from: node.from, options: properties(), validFor: identifier$1 };
                return { from: node.from, options: tags, validFor: identifier$1 };
            }

        }
        if (!context.explicit)
            return null;
        let above = node.resolve(pos), before = above.childBefore(pos);
        if (before && before.name == ":" && above.name == "PseudoClassSelector")
            return { from: pos, options: pseudoClasses, validFor: identifier$1 };
        if (before && before.name == ":" && above.name == "Declaration" || above.name == "ArgList")
            return { from: pos, options: values, validFor: identifier$1 };
        if (above.name == "Block" || above.name == "Styles")
            return { from: pos, options: properties(), validFor: identifier$1 };
        return null;
    };

*/