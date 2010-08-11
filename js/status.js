function treeToggle() {
  var toggle = document.getElementById("toggle");

  if (toggle.src.match("/green-plus.png$")) {
    expandTree("stree");
    toggle.src = "/images/green-minus.png";
    toggle.alt = "Collapse";
  }
  else {
    collapseTree("stree");
    expandToItem("stree", "first_module");
    toggle.src = "/images/green-plus.png";
    toggle.alt = "Expand";
  }
}

window.onload = function() {
  // pop open the tree to the module level
  expandToItem("stree", "first_module");

  // hook up the expand/collapse button
  document.getElementById("toggle").onclick = treeToggle;
};
