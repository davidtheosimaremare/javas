import { unref, withCtx, createVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { Head } from "@inertiajs/vue3";
import { M as MainLayout } from "./MainLayout-CefFmm5j.js";
const _sfc_main = /* @__PURE__ */ Object.assign({ layout: MainLayout }, {
  __name: "Home",
  __ssrInlineRender: true,
  props: { name: String },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>Distributor Resmi Siemens &amp; G-COMIN - JBB</title><meta name="description" content="Javas Berkah Bistari (JBB) adalah distributor resmi produk listrik Siemens low voltage dan lampu industrial G-COMIN. Solusi EPC Power terpercaya."${_scopeId}><meta property="og:title" content="Distributor Resmi Siemens Indonesia - JBB"${_scopeId}><meta property="og:description" content="Solusi kelistrikan dan lampu industrial terbaik."${_scopeId}><meta property="og:image" content="https://websiteanda.com/gambar-share.jpg"${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, "Distributor Resmi Siemens & G-COMIN - JBB"),
              createVNode("meta", {
                name: "description",
                content: "Javas Berkah Bistari (JBB) adalah distributor resmi produk listrik Siemens low voltage dan lampu industrial G-COMIN. Solusi EPC Power terpercaya."
              }),
              createVNode("meta", {
                property: "og:title",
                content: "Distributor Resmi Siemens Indonesia - JBB"
              }),
              createVNode("meta", {
                property: "og:description",
                content: "Solusi kelistrikan dan lampu industrial terbaik."
              }),
              createVNode("meta", {
                property: "og:image",
                content: "https://websiteanda.com/gambar-share.jpg"
              })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="container py-5"><div class="p-5 mb-4 bg-light rounded-3 border"><div class="container-fluid py-5"><h1 class="display-5 fw-bold">Selamat Datang di JBB New</h1><p class="col-md-8 fs-4">Halo, ${ssrInterpolate(__props.name)}! Layout ini sudah menggunakan Bootstrap 5 dan Inertia Persistent Layout.</p><button class="btn btn-primary btn-lg" type="button">Coba Klik Tombol</button></div></div></div><!--]-->`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Home.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
