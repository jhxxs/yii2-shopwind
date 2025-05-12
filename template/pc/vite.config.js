import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

/**
 * 按需加载速度太慢
 */
//import AutoImport from 'unplugin-auto-import/vite'
//import Components from 'unplugin-vue-components/vite'
//import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    /*AutoImport({
      resolvers: [ElementPlusResolver()],
    }),
    Components({
      resolvers: [ElementPlusResolver()],
    })*/
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src')
    }
  },
  envDir: "env",
  //transpileDependencies: [/node_modules/]
  base: process.env.NODE_ENV === 'production' ? "/pc/" : "/",
  server: {
    port: 8080,
    open: true,
    proxy: {
      '/api': {
        target: 'http://192.168.1.41/',
        changeOrigin: true,
      } 
    }
  }
})
