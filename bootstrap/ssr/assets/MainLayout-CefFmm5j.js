import { mergeProps, unref, withCtx, createTextVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderSlot } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main = {
  __name: "MainLayout",
  __ssrInlineRender: true,
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "d-flex flex-column min-vh-100" }, _attrs))} data-v-21eab194><nav class="navbar navbar-expand-lg navbar-dark bg-primary" data-v-21eab194><div class="container" data-v-21eab194>`);
      _push(ssrRenderComponent(unref(Link), {
        class: "navbar-brand fw-bold",
        href: "/"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`JBB Project`);
          } else {
            return [
              createTextVNode("JBB Project")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" data-v-21eab194><span class="navbar-toggler-icon" data-v-21eab194></span></button><div class="collapse navbar-collapse" id="navbarNav" data-v-21eab194><ul class="navbar-nav ms-auto" data-v-21eab194><li class="nav-item" data-v-21eab194>`);
      _push(ssrRenderComponent(unref(Link), {
        class: "nav-link",
        href: "/"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Home`);
          } else {
            return [
              createTextVNode("Home")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li class="nav-item" data-v-21eab194>`);
      _push(ssrRenderComponent(unref(Link), {
        class: "nav-link",
        href: "/about"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`About`);
          } else {
            return [
              createTextVNode("About")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li class="nav-item" data-v-21eab194>`);
      _push(ssrRenderComponent(unref(Link), {
        class: "nav-link",
        href: "/contact"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Contact`);
          } else {
            return [
              createTextVNode("Contact")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li></ul></div></div></nav><main class="flex-grow-1" data-v-21eab194>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</main><footer class="bg-dark text-white py-4 mt-auto" data-v-21eab194><div class="container text-center" data-v-21eab194><small data-v-21eab194>© 2025 Javas Berkah Bistari. All rights reserved.</small></div></footer></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/MainLayout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const MainLayout = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-21eab194"]]);
export {
  MainLayout as M
};
