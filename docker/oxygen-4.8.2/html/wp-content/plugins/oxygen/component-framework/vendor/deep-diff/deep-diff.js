/**
 * Modified from https://github.com/flitbit/diff
 */
;(function(root) {
    // nodejs compatible on server side and in the browser.
    function inherits(ctor, superCtor) {
        ctor.super_ = superCtor;
        ctor.prototype = Object.create(superCtor.prototype, {
            constructor: {
                value: ctor,
                enumerable: false,
                writable: true,
                configurable: true
            }
        });
    }

    function Diff(kind, path) {
        Object.defineProperty(this, "kind", {
            value: kind,
            enumerable: true
        });
        if (path && path.length) {
            Object.defineProperty(this, "path", {
                value: path,
                enumerable: true
            });
        }
    }

    function DiffEdit(path, origin, value) {
        DiffEdit.super_.call(this, "EDIT", path);
        Object.defineProperty(this, "lhs", {
            value: origin,
            enumerable: true
        });
        Object.defineProperty(this, "rhs", {
            value: value,
            enumerable: true
        });
    }
    inherits(DiffEdit, Diff);

    function DiffNew(path, value) {
        DiffNew.super_.call(this, "NEW", path);
        Object.defineProperty(this, "rhs", {
            value: value,
            enumerable: true
        });
    }
    inherits(DiffNew, Diff);

    function DiffDeleted(path, value) {
        DiffDeleted.super_.call(this, "DELETE", path);
        Object.defineProperty(this, "lhs", {
            value: value,
            enumerable: true
        });
    }
    inherits(DiffDeleted, Diff);

    function DiffArray(path, index, item) {
        DiffArray.super_.call(this, "ARRAY", path);
        Object.defineProperty(this, "index", {
            value: index,
            enumerable: true
        });
        Object.defineProperty(this, "item", {
            value: item,
            enumerable: true
        });
    }
    inherits(DiffArray, Diff);

    function realTypeOf(subject) {
        var type = typeof subject;
        if (type !== "object") {
            return type;
        }

        if (subject === Math) {
            return "math";
        } else if (subject === null) {
            return "null";
        } else if (Array.isArray(subject)) {
            return "array";
        } else if (
            Object.prototype.toString.call(subject) === "[object Date]"
        ) {
            return "date";
        } else if (
            typeof subject.toString === "function" &&
            /^\/.*\//.test(subject.toString())
        ) {
            return "regexp";
        }
        return "object";
    }

    // http://werxltd.com/wp/2010/05/13/javascript-implementation-of-javas-string-hashcode-method/
    function hashThisString(string) {
        var hash = 0;
        if (string.length === 0) {
            return hash;
        }
        for (var i = 0; i < string.length; i++) {
            var char = string.charCodeAt(i);
            hash = (hash << 5) - hash + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash;
    }

    // Gets a hash of the given object in an array order-independent fashion
    // also object key order independent (easier since they can be alphabetized)
    function getOrderIndependentHash(object) {
        var accum = 0;
        var type = realTypeOf(object);

        if (type === "array") {
            object.forEach(function(item) {
                // Addition is commutative so this is order indep
                accum += getOrderIndependentHash(item);
            });

            var arrayString = "[type: array, hash: " + accum + "]";
            return accum + hashThisString(arrayString);
        }

        if (type === "object") {
            for (var key in object) {
                if (object.hasOwnProperty(key)) {
                    var keyValueString =
                        "[ type: object, key: " +
                        key +
                        ", value hash: " +
                        getOrderIndependentHash(object[key]) +
                        "]";
                    accum += hashThisString(keyValueString);
                }
            }

            return accum;
        }

        // Non object, non array...should be good?
        var stringToHash = "[ type: " + type + " ; value: " + object + "]";
        return accum + hashThisString(stringToHash);
    }

    function deepDiff(lhs, rhs, changes, prefilter, path, key, stack, orderIndependent) {
        changes = changes || [];
        path = path || [];
        stack = stack || [];
        var currentPath = path.slice(0);
        if (typeof key !== "undefined" && key !== null) {
            if (prefilter) {
                if (
                    typeof prefilter === "function" &&
                    prefilter(currentPath, key)
                ) {
                    return;
                } else if (typeof prefilter === "object") {
                    if (
                        prefilter.prefilter &&
                        prefilter.prefilter(currentPath, key)
                    ) {
                        return;
                    }
                    if (prefilter.normalize) {
                        var alt = prefilter.normalize(currentPath, key, lhs, rhs);
                        if (alt) {
                            lhs = alt[0];
                            rhs = alt[1];
                        }
                    }
                }
            }
            currentPath.push(key);
        }

        // Use string comparison for regexes
        if (realTypeOf(lhs) === "regexp" && realTypeOf(rhs) === "regexp") {
            lhs = lhs.toString();
            rhs = rhs.toString();
        }

        var ltype = typeof lhs;
        var rtype = typeof rhs;
        var i, j, k, other;

        var ldefined =
            ltype !== "undefined" ||
            (stack &&
                stack.length > 0 &&
                stack[stack.length - 1].lhs &&
                Object.getOwnPropertyDescriptor(
                    stack[stack.length - 1].lhs,
                    key
                ));
        var rdefined =
            rtype !== "undefined" ||
            (stack &&
                stack.length > 0 &&
                stack[stack.length - 1].rhs &&
                Object.getOwnPropertyDescriptor(
                    stack[stack.length - 1].rhs,
                    key
                ));

        if (!ldefined && rdefined) {
            changes.push(new DiffNew(currentPath, rhs));
        } else if (!rdefined && ldefined) {
            changes.push(new DiffDeleted(currentPath, lhs));
        } else if (realTypeOf(lhs) !== realTypeOf(rhs)) {
            changes.push(new DiffEdit(currentPath, lhs, rhs));
        } else if (realTypeOf(lhs) === "date" && lhs - rhs !== 0) {
            changes.push(new DiffEdit(currentPath, lhs, rhs));
        } else if (ltype === "object" && lhs !== null && rhs !== null) {
            for (i = stack.length - 1; i > -1; --i) {
                if (stack[i].lhs === lhs) {
                    other = true;
                    break;
                }
            }
            if (!other) {
                stack.push({ lhs: lhs, rhs: rhs });
                if (Array.isArray(lhs)) {
                    // If order doesn't matter, we need to sort our arrays
                    if (orderIndependent) {
                        lhs.sort(function(a, b) {
                            return (getOrderIndependentHash(a) - getOrderIndependentHash(b));
                        });

                        rhs.sort(function(a, b) {
                            return (getOrderIndependentHash(a) - getOrderIndependentHash(b));
                        });
                    }
                    i = rhs.length - 1;
                    j = lhs.length - 1;
                    while (i > j) {
                        changes.push(
                            new DiffArray(
                                currentPath,
                                i,
                                new DiffNew(undefined, rhs[i--])
                            )
                        );
                    }
                    while (j > i) {
                        changes.push(
                            new DiffArray(
                                currentPath,
                                j,
                                new DiffDeleted(undefined, lhs[j--])
                            )
                        );
                    }
                    for (; i >= 0; --i) {
                        deepDiff(lhs[i], rhs[i], changes, prefilter, currentPath, i, stack, orderIndependent);
                    }
                } else {
                    var akeys = Object.keys(lhs);
                    var pkeys = Object.keys(rhs);
                    for (i = 0; i < akeys.length; ++i) {
                        k = akeys[i];
                        other = pkeys.indexOf(k);
                        if (other >= 0) {
                            deepDiff(lhs[k], rhs[k], changes, prefilter, currentPath, k, stack, orderIndependent);
                            pkeys[other] = null;
                        } else {
                            deepDiff(lhs[k], undefined, changes, prefilter, currentPath, k, stack, orderIndependent);
                        }
                    }
                    for (i = 0; i < pkeys.length; ++i) {
                        k = pkeys[i];
                        if (k) {
                            deepDiff(undefined, rhs[k], changes, prefilter, currentPath, k, stack, orderIndependent);
                        }
                    }
                }
                stack.length = stack.length - 1;
            } else if (lhs !== rhs) {
                // lhs is contains a cycle at this element and it differs from rhs
                changes.push(new DiffEdit(currentPath, lhs, rhs));
            }
        } else if (lhs !== rhs) {
            if (!(ltype === "number" && isNaN(lhs) && isNaN(rhs))) {
                changes.push(new DiffEdit(currentPath, lhs, rhs));
            }
        }
    }

    function observableDiff(lhs, rhs, observer, prefilter, orderIndependent) {
        var changes = [];
        deepDiff(lhs, rhs, changes, prefilter, null, null, null, orderIndependent);
        if (observer) {
            for (var i = 0; i < changes.length; ++i) {
                observer(changes[i]);
            }
        }
        return changes;
    }

    function accumulateDiff(lhs, rhs, prefilter, accum) {
        var observer = accum
            ? function(difference) {
                  if (difference) {
                      accum.push(difference);
                  }
              }
            : undefined;
        var changes = observableDiff(lhs, rhs, observer, prefilter);
        return accum ? accum : changes.length ? changes : undefined;
    }

    Object.defineProperties(accumulateDiff, {
        diff: {
            value: accumulateDiff,
            enumerable: true
        },
        observableDiff: {
            value: observableDiff,
            enumerable: true
        },
        orderIndepHash: {
            value: getOrderIndependentHash,
            enumerable: true
        }
    });

    root.DeepDiff = accumulateDiff;
}(this));