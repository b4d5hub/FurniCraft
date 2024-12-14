(()=>{var e,t,o,r={585:(e,t,o)=>{"use strict";o.r(t);const r=window.wc.wcSettings;var c=o(1609),i=o(5573);const l=(0,c.createElement)(i.SVG,{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"},(0,c.createElement)(i.Path,{fillRule:"evenodd",clipRule:"evenodd",d:"M6 10C7.10457 10 8 9.10457 8 8C8 6.89543 7.10457 6 6 6C4.89543 6 4 6.89543 4 8C4 9.10457 4.89543 10 6 10ZM20 8.75H11.1111V7.25L20 7.25V8.75ZM20 15.75L11.1111 15.75V14.25L20 14.25V15.75ZM8 15C8 16.1046 7.10457 17 6 17C4.89543 17 4 16.1046 4 15C4 13.8954 4.89543 13 6 13C7.10457 13 8 13.8954 8 15Z",fill:"currentColor"})),s=window.wp.blocks,n=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","name":"woocommerce/product-filter-chips","version":"1.0.0","title":"Chips","description":"Display filter options as chips.","category":"woocommerce","keywords":["WooCommerce"],"textdomain":"woocommerce","apiVersion":3,"ancestor":["woocommerce/product-filter-attribute"],"supports":{},"usesContext":["filterData"],"attributes":{"chipText":{"type":"string"},"customChipText":{"type":"string"},"chipBackground":{"type":"string"},"customChipBackground":{"type":"string"},"chipBorder":{"type":"string"},"customChipBorder":{"type":"string"},"selectedChipText":{"type":"string"},"customSelectedChipText":{"type":"string"},"selectedChipBackground":{"type":"string"},"customSelectedChipBackground":{"type":"string"},"selectedChipBorder":{"type":"string"},"customSelectedChipBorder":{"type":"string"}}}');var p=o(7723),d=o(6087),a=o(851);const u=window.wp.blockEditor;function h(e,t){return e?`var(--wp--preset--color--${e})`:t||""}function m(e){const{chipText:t,chipBackground:o,chipBorder:r,selectedChipText:c,selectedChipBackground:i,selectedChipBorder:l,customChipText:s,customChipBackground:n,customChipBorder:p,customSelectedChipText:d,customSelectedChipBackground:a,customSelectedChipBorder:u}=e,m={"--wc-product-filter-chips-text":h(t,s),"--wc-product-filter-chips-background":h(o,n),"--wc-product-filter-chips-border":h(r,p),"--wc-product-filter-chips-selected-text":h(c,d),"--wc-product-filter-chips-selected-background":h(i,a),"--wc-product-filter-chips-selected-border":h(l,u)};return Object.keys(m).reduce(((e,t)=>(m[t]&&(e[t]=m[t]),e)),{})}function C(e){const{chipText:t,chipBackground:o,chipBorder:r,selectedChipText:c,selectedChipBackground:i,selectedChipBorder:l,customChipText:s,customChipBackground:n,customChipBorder:p,customSelectedChipText:d,customSelectedChipBackground:a,customSelectedChipBorder:u}=e;return{"has-chip-text-color":t||s,"has-chip-background-color":o||n,"has-chip-border-color":r||p,"has-selected-chip-text-color":c||d,"has-selected-chip-background-color":i||a,"has-selected-chip-border-color":l||u}}o(8669);const g=(0,u.withColors)({chipText:"chip-text",chipBorder:"chip-border",chipBackground:"chip-background",selectedChipText:"selected-chip-text",selectedChipBorder:"selected-chip-border",selectedChipBackground:"selected-chip-background"})((e=>{const t=(0,u.__experimentalUseMultipleOriginColorsAndGradients)(),{context:o,clientId:r,attributes:i,setAttributes:l,chipText:s,setChipText:n,chipBackground:h,setChipBackground:g,chipBorder:w,setChipBorder:k,selectedChipText:b,setSelectedChipText:f,selectedChipBackground:B,setSelectedChipBackground:x,selectedChipBorder:y,setSelectedChipBorder:_}=e,{customChipText:v,customChipBackground:S,customChipBorder:T,customSelectedChipText:O,customSelectedChipBackground:E,customSelectedChipBorder:j}=i,{filterData:M}=o,{isLoading:V,items:A}=M,N=(0,u.useBlockProps)({className:(0,a.A)("wc-block-product-filter-chips",{"is-loading":V,...C(i)}),style:m(i)}),P=(0,d.useMemo)((()=>[...Array(10)].map(((e,t)=>(0,c.createElement)("div",{className:"wc-block-product-filter-chips__item",key:t,style:{width:Math.floor(75*Math.random())+"%"}}," ")))),[]);if(!A)return(0,c.createElement)(c.Fragment,null);const F=A.length>15;return(0,c.createElement)(c.Fragment,null,(0,c.createElement)("div",{...N},(0,c.createElement)("div",{className:"wc-block-product-filter-chips__items"},V&&P,!V&&(F?A.slice(0,15):A).map(((e,t)=>(0,c.createElement)("div",{key:t,className:"wc-block-product-filter-chips__item","aria-checked":!!e.selected},(0,c.createElement)("span",{className:"wc-block-product-filter-chips__label"},e.label))))),!V&&F&&(0,c.createElement)("button",{className:"wc-block-product-filter-chips__show-more"},(0,p.__)("Show more…","woocommerce"))),(0,c.createElement)(u.InspectorControls,{group:"color"},t.hasColorsOrGradients&&(0,c.createElement)(u.__experimentalColorGradientSettingsDropdown,{__experimentalIsRenderedInSidebar:!0,settings:[{label:(0,p.__)("Unselected Chip Text","woocommerce"),colorValue:s.color||v,onColorChange:e=>{n(e),l({customChipText:e})},resetAllFilter:()=>{n(""),l({customChipText:""})}},{label:(0,p.__)("Unselected Chip Border","woocommerce"),colorValue:w.color||T,onColorChange:e=>{k(e),l({customChipBorder:e})},resetAllFilter:()=>{k(""),l({customChipBorder:""})}},{label:(0,p.__)("Unselected Chip Background","woocommerce"),colorValue:h.color||S,onColorChange:e=>{g(e),l({customChipBackground:e})},resetAllFilter:()=>{g(""),l({customChipBackground:""})}},{label:(0,p.__)("Selected Chip Text","woocommerce"),colorValue:b.color||O,onColorChange:e=>{f(e),l({customSelectedChipText:e})},resetAllFilter:()=>{f(""),l({customSelectedChipText:""})}},{label:(0,p.__)("Selected Chip Border","woocommerce"),colorValue:y.color||j,onColorChange:e=>{_(e),l({customSelectedChipBorder:e})},resetAllFilter:()=>{_(""),l({customSelectedChipBorder:""})}},{label:(0,p.__)("Selected Chip Background","woocommerce"),colorValue:B.color||E,onColorChange:e=>{x(e),l({customSelectedChipBackground:e})},resetAllFilter:()=>{x(""),l({customSelectedChipBackground:""})}}],panelId:r,...t})))}));o(9190),(()=>{const{experimentalBlocksEnabled:e}=(0,r.getSetting)("wcBlocksConfig",{experimentalBlocksEnabled:!1});return e})()&&(0,s.registerBlockType)(n,{edit:g,icon:l,save:({attributes:e,style:t})=>{const o=u.useBlockProps.save({className:(0,a.A)("wc-block-product-filter-chips",e.className,C(e)),style:{...t,...m(e)}});return(0,c.createElement)("div",{...o})}})},8669:()=>{},9190:()=>{},1609:e=>{"use strict";e.exports=window.React},6087:e=>{"use strict";e.exports=window.wp.element},7723:e=>{"use strict";e.exports=window.wp.i18n},5573:e=>{"use strict";e.exports=window.wp.primitives}},c={};function i(e){var t=c[e];if(void 0!==t)return t.exports;var o=c[e]={exports:{}};return r[e].call(o.exports,o,o.exports,i),o.exports}i.m=r,e=[],i.O=(t,o,r,c)=>{if(!o){var l=1/0;for(d=0;d<e.length;d++){for(var[o,r,c]=e[d],s=!0,n=0;n<o.length;n++)(!1&c||l>=c)&&Object.keys(i.O).every((e=>i.O[e](o[n])))?o.splice(n--,1):(s=!1,c<l&&(l=c));if(s){e.splice(d--,1);var p=r();void 0!==p&&(t=p)}}return t}c=c||0;for(var d=e.length;d>0&&e[d-1][2]>c;d--)e[d]=e[d-1];e[d]=[o,r,c]},i.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return i.d(t,{a:t}),t},o=Object.getPrototypeOf?e=>Object.getPrototypeOf(e):e=>e.__proto__,i.t=function(e,r){if(1&r&&(e=this(e)),8&r)return e;if("object"==typeof e&&e){if(4&r&&e.__esModule)return e;if(16&r&&"function"==typeof e.then)return e}var c=Object.create(null);i.r(c);var l={};t=t||[null,o({}),o([]),o(o)];for(var s=2&r&&e;"object"==typeof s&&!~t.indexOf(s);s=o(s))Object.getOwnPropertyNames(s).forEach((t=>l[t]=()=>e[t]));return l.default=()=>e,i.d(c,l),c},i.d=(e,t)=>{for(var o in t)i.o(t,o)&&!i.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},i.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),i.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.j=7857,(()=>{var e={7857:0};i.O.j=t=>0===e[t];var t=(t,o)=>{var r,c,[l,s,n]=o,p=0;if(l.some((t=>0!==e[t]))){for(r in s)i.o(s,r)&&(i.m[r]=s[r]);if(n)var d=n(i)}for(t&&t(o);p<l.length;p++)c=l[p],i.o(e,c)&&e[c]&&e[c][0](),e[c]=0;return i.O(d)},o=self.webpackChunkwebpackWcBlocksMainJsonp=self.webpackChunkwebpackWcBlocksMainJsonp||[];o.forEach(t.bind(null,0)),o.push=t.bind(null,o.push.bind(o))})();var l=i.O(void 0,[94],(()=>i(585)));l=i.O(l),((this.wc=this.wc||{}).blocks=this.wc.blocks||{})["product-filter-chips"]=l})();