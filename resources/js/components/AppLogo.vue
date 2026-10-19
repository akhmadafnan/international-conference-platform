<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const { t } = useI18n();
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ms-1 grid min-w-0 flex-1 text-start text-sm">
        <span class="truncate leading-tight font-semibold">ICHES</span>
        <span class="truncate text-xs text-sidebar-foreground/70">{{ t('navigation.platform') }}</span>
    </div>
</template>
