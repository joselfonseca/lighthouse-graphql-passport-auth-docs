window.docsearch = require('docsearch.js');
import hljs from 'highlight.js';
import hljsDefineGraphQL from "highlightjs-graphql";

hljsDefineGraphQL(hljs);

document.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('pre code').forEach((block) => {
        hljs.highlightBlock(block);
    });
});
