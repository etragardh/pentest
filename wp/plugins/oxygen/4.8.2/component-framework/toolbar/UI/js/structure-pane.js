let dropTarget = {
    parentElement: null,
    parent: null,
    index: null,
    before: false
}

let dragTarget = {
    element: null,
    parent: null,
    index: null
}

let expandOnHover = null;

let throttler =  null;
let dragOverThrottler = false;

let leavethrottle = null;

// only possible children
let onlyPossibleChildren = {
    'ct_ul': [
        'ct_li'
    ],
    'oxy_dynamic_list': [
        'ct_div_block',
    ],
    'ct_slider': [
        'ct_slide'
    ],
    'ct_columns': [
        'ct_column'
    ],
    'ct_new_columns': [
        'ct_div_block'
    ],
    'oxy_tabs': [
        'oxy_tab'
    ],
    'oxy_tabs_contents': [
        'oxy_tab_content'
    ],
    'oxy_header': [
        'oxy_header_row'
    ],
    'oxy_header_row': [
        'oxy_header_row_left',
        'oxy_header_row_center',
        'oxy_header_row_right',
    ]
}

// only possible parents
let onlyPossibleParents = {
    'ct_li': [
        'ct_ul'
    ],
    'ct_slide': [
        'ct_slider'
    ],
    'ct_column': [
        'ct_columns'
    ],
    'ct_tab': [
        'ct_tabs'
    ],
    'oxy_tab_content': [
        'oxy_tabs_contents'
    ],
    'oxy_header_row': [
        'oxy_header'
    ],
    'oxy_header_row_left': [
        'oxy_header_row'
    ],
    'oxy_header_row_center': [
        'oxy_header_row'
    ],
    'oxy_header_row_right': [
        'oxy_header_row'
    ]
}

// only allowed children
let notAllowedChildren = {
    'ct_section': [
        'ct_section',
        'oxy_header'
    ],
    'oxy_header': [
        'oxy_header',
        'ct_section'
    ],
    'ct_link': [
        'ct_link',
        'ct_link_text'
    ]
}

function validDrop() {

    let source = dragTarget.element.getAttribute('data-component-name');
    let destination = dropTarget.parentElement.getAttribute('data-component-name');

    // do not allow to insert in repeater
    if( destination === 'oxy_dynamic_list') {
        return false;
    }

    // check API components for any parent/child restrictions
    if (!$scope.iframeScope.canBeChild(destination, source)) {
        return false;
    }


    if( onlyPossibleChildren.hasOwnProperty(destination)) {
        if(onlyPossibleChildren[destination].indexOf(source) === -1) {
            return false;
        }
    }

    if( onlyPossibleParents.hasOwnProperty(source)) {
        if(onlyPossibleParents[source].indexOf(destination) === -1) {
            return false;
        }
    }

    if( notAllowedChildren.hasOwnProperty(destination)) {
        if(notAllowedChildren[destination].indexOf(source) !== -1) {
            return false;
        }
    }

    // don't allow to insert any component inside itself at any deep
    var exceptions = ['ct_div_block'];
    if (exceptions.indexOf(source) < 0 ) {
        if(!notAllowedChildren.hasOwnProperty(source)) {
            notAllowedChildren[source] = [];
        }
        if(notAllowedChildren[source].indexOf(source) === -1) {
            notAllowedChildren[source].push(source);
        }
    }

    // loop trough parents
    if( notAllowedChildren.hasOwnProperty(source)) {
        let found = false;
        angular.element(dropTarget.parentElement).parents().each( (key,parent) => {
            let name = angular.element(parent).attr('data-component-name');
            if (name && notAllowedChildren[source].indexOf(name) !== -1) {
                found = true;
                return false;
            }
            if (angular.element(parent).is('#ct-dom-tree-2')) {
                return false;
            }
        })

        if (found) {
            return false;
        }
    }

    return true;
}

function treeClean() {

    Array.from(document.querySelectorAll('.dom-tree-node-drop-after')).forEach( node => {
        node.classList.remove('dom-tree-node-drop-after');
        node.classList.remove('dom-tree-node-drop-inside');
    })

    Array.from(document.querySelectorAll('.dom-tree-node-drop-before')).forEach( node => {
        node.classList.remove('dom-tree-node-drop-before');
    })

}
function treeDrop(e) {
    e.preventDefault();
    
    $scope.iframeScope.componentsReorderTree(dragTarget.element, dragTarget.parent, dropTarget.parent, dragTarget.index, dropTarget.index - (dropTarget.parent === 0 ? 1 : 0) + (dropTarget.before ? 0 : 1));

    if(dropTarget.parentElement) {
        $scope.$broadcast('nodeExpand', dropTarget.parentElement);
    }

    setTimeout( treeClean, 100);
}

function treeDragStart(e) {
    
    let domnode = e.target.closest('.dom-tree-node');

    dragTarget.element = domnode;
    dragTarget.index = Array.from(domnode.parentNode.children).indexOf(domnode);

    let domnodeParent = domnode.parentNode.closest('.dom-tree-node');

    if( domnodeParent ) {
        dragTarget.parent = domnodeParent.getAttribute('ng-attr-tree-id');
    }
    else {
        
        dragTarget.parent = 0;
    }

    domnode.classList.add('dom-tree-node-dragged')
    
    e.dataTransfer.setDragImage(domnode.querySelector('.dom-tree-node-label'), e.offsetX, e.offsetY)

}

function treeDragEnd(e) {
    Array.from(document.querySelectorAll('.dom-tree-node-dragged')).forEach( node => {
        node.classList.remove('dom-tree-node-dragged');
    })

    dragTarget.element.classList.remove('dom-tree-dragged');
}

function treeDragLeave(e) {

    dropTarget.parentElement = null;
    dropTarget.parent = null;
    dropTarget.index = null;
    dropTarget.before = false;

    if( expandOnHover ) {
        clearTimeout( expandOnHover );
        expandOnHover = null;
    }

    if( leavethrottle ) {
        clearTimeout( leavethrottle );
        leavethrottle = null;
    }

    leavethrottle = setTimeout(function() {
        treeClean();
        dragTarget.element.classList.remove('dom-tree-dragged');
        clearTimeout(leavethrottle)
        leavethrottle = null;
    }, 1000)

}

function treeDragOver(e) {
    
    // vertical center of a tree node, to figure out before/after index
    let verticalCenter = 22;
    

    let target = e.target.closest('.dom-tree-node-label');

    if(!target) {
        return false;
    }
    
    // dragging is still underway, so no need to run the leave routine.
    if(leavethrottle) {
        clearTimeout(leavethrottle);
        leavethrottle = null;
    }

    // remove placeholders from the last process
    treeClean();

    let domnode = target.closest('.dom-tree-node');

    
    if(domnode === dragTarget.element) {
        dragTarget.element.classList.remove('dom-tree-dragged');
        return false;
    }

    let subtree = domnode.querySelector('.sub-tree');

    var goingUnder = (e.offsetY >= verticalCenter && e.offsetX > 40 )

    // a node with an expanded sub-tree cannot show a placeholder 'after' its label
    if(subtree && subtree.children.length && !subtree.classList.contains('collapsed') && e.offsetY >= verticalCenter) {
        return false;
    }

    if(dragTarget.element === domnode || (dragTarget.element === domnode.previousElementSibling && e.offsetY < verticalCenter ) ) {
        return false;
    }
    
    let parent = domnode.parentNode.closest('.dom-tree-node');
    
    if(parent) {
        dropTarget.parent = parent.getAttribute('ng-attr-tree-id');
        dropTarget.parentElement = parent;
    } else {
        dropTarget.parent = 0;
        dropTarget.parentElement = document.querySelector('.dom-tree-node-root');
    }

    let newDropIndex = Array.from(domnode.parentNode.children).indexOf(domnode);

    dropTarget.index = newDropIndex;
    
    if(goingUnder && domnode.classList.contains('ct_accepts_drop')) {
        domnode.classList.add('dom-tree-node-drop-inside');	
        dropTarget.parent = domnode.getAttribute('ng-attr-tree-id');
        dropTarget.index = 0;
        if( subtree && subtree.children.length ) {
            dropTarget.index = -1;
        }
        dropTarget.parentElement = domnode;
    } else if( dragTarget.element === domnode.nextElementSibling && e.offsetY >= verticalCenter ) {
        return false;
    }

    

    if( dropTarget.parentElement || dropTarget.parent == 0) {
        if( !validDrop() ) {
            return false
        }
    }

    dragTarget.element.classList.add('dom-tree-dragged');

    if(e.offsetY < verticalCenter) {
        domnode.classList.add('dom-tree-node-drop-before');
        dropTarget.before = true;

    } else {
        domnode.classList.add('dom-tree-node-drop-after');	
        dropTarget.before = false;
    }

    e.preventDefault();
    // debugger;
    return true;
}

function nodeShowMoreOptions(e) {
    let target = e.target.closest('.dom-tree-node');
    $scope.$broadcast('nodeShowMore', target.getAttribute('ng-attr-tree-id'));
}

function onLabelClick(e) {
    if( !e.target.closest('input.title') && !e.target.closest('.rename-trigger') && !e.target.closest('.option-categorize') ) {
        // the purpose is to execute blur operation on the rename input field
        $scope.$broadcast('nodeShowMore', null);
    }
}

document.querySelector('body').addEventListener('click', function(e) {
    $scope.$broadcast('nodeShowMore', null);
})