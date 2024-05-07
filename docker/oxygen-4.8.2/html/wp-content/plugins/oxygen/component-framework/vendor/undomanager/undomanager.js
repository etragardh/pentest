/**
 * Undo Redo manager for Oxygen builder
 * Customized from https://github.com/ArthurClemens/Javascript-Undo-Manager
 */
;(function() {
	'use strict';

    var UndoManager = function() {

        var commands = [],
            index = -1,
            limit = 0,
            isExecuting = false,
            changeCallback,
            doCallback;

        var execute = function(command, action) {
            if (!command || typeof command[action] !== "function") {
                return this;
            }
            isExecuting = true;

            command[action]();

            isExecuting = false;
            return this;
        };

        return {
            add: function (command) {
                if (isExecuting) {
                    return this;
                }
                // if we are here after having called undo,
                // invalidate items higher on the stack
                commands.splice(index + 1, commands.length - index);

                commands.push(command);

                // Delete old entries to limit the history items
                if (limit && commands.length > limit) {
                    commands = commands.slice(-limit);
                }

                // set the current index to the end
                index = commands.length - 1;
                if (changeCallback) {
                    changeCallback();
                }
                return this;
            },

            setChangeCallback: function (callback) {
                changeCallback = callback;
            },
            
            setDoCallback: function (callback) {
                doCallback = callback;
            },
            
            do: function (doIndex) {
                if (doIndex !== index && commands.length && doIndex < commands.length && typeof doCallback === "function") {
                    var command = commands[doIndex];
                    if (command) {
                        doCallback.call(this, command);
                        
                        index = doIndex;
                        if (changeCallback) {
                            changeCallback();
                        }
                    }
                }
                return this;
            },

            undo: function () {
                var command = commands[index];
                if (!command) {
                    return this;
                }
                execute(command, "undo");
                index -= 1;
                if (changeCallback) {
                    changeCallback();
                }
                return this;
            },

            redo: function () {
                var command = commands[index + 1];
                if (!command) {
                    return this;
                }
                execute(command, "redo");
                index += 1;
                if (changeCallback) {
                    changeCallback();
                }
                return this;
            },

            clear: function () {
                var prev_size = commands.length;

                commands = [];
                index = -1;

                if (changeCallback && (prev_size > 0)) {
                    changeCallback();
                }
            },

            hasUndo: function () {
                return index !== -1;
            },

            hasRedo: function () {
                return index < (commands.length - 1);
            },

            getCommands: function () {
                return commands;
            },

            getIndex: function() {
                return index;
            },

            setIndex: function(i) {
                index = i;

                if (changeCallback) {
                    changeCallback();
                }
            },

            setLimit: function(l) {
                limit = l;
            }
        };
    };

	window.UndoManager = UndoManager;
}());
