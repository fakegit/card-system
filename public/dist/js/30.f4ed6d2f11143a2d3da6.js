webpackJsonp([30],{oF1k:function(t,e,n){"use strict";var a,s,c;Object.defineProperty(e,"__esModule",{value:!0}),a={components:{},data:function(){return{}},mounted:function(){},methods:{clearCache:function(){var t=this;this.$http.post("web/cache/clear").then(function(){t.$notify({title:"成功",message:"系统缓存已经成功清除，若仍有问题请检查CDN缓存",type:"success"})})}}},s={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"app-container"},[t._v("\n  这里是一些系统功能\n  "),n("div",{staticStyle:{"margin-top":"24px"}},[n("el-button",{attrs:{size:"small"},on:{click:t.clearCache}},[t._v("清空缓存")])],1),t._v(" "),n("div",{staticStyle:{"margin-top":"24px"}},[n("el-button",{attrs:{size:"small"}},[n("a",{attrs:{href:"/admin/logs",target:"_blank"}},[t._v("系统日志")])])],1)])},staticRenderFns:[]},c=n("VU/8")(a,s,!1,null,null,null),e.default=c.exports}});