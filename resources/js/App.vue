<script setup lang="ts">
import { computed, watchEffect } from 'vue'
import { useI18n } from 'vue-i18n'
import { ConfigProvider } from 'reka-ui'
import PublicHome from '@/pages/public/Home.vue'
import { getLocaleDirection, syncDocumentLocale } from '@/i18n'

const { locale } = useI18n()

const direction = computed(() => getLocaleDirection(locale.value))

watchEffect(() => {
  syncDocumentLocale(locale.value)
})
</script>

<template>
  <ConfigProvider :dir="direction">
    <PublicHome />
  </ConfigProvider>
</template>
