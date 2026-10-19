<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ConfigProvider } from 'reka-ui';
import { SidebarProvider } from '@/components/ui/sidebar';
import type { AppVariant } from '@/types';

type Props = {
    variant?: AppVariant;
};

withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});

const page = usePage();
const isOpen = page.props.sidebarOpen;
</script>

<template>
    <ConfigProvider
        :dir="
            page.component.startsWith('admin/')
                ? page.props.localization.direction
                : undefined
        "
    >
        <div
            v-if="variant === 'header'"
            class="workspace-shell flex min-h-screen w-full flex-col"
        >
            <slot />
        </div>
        <SidebarProvider
            v-else
            class="workspace-shell"
            :class="{
                'admin-workspace admin-lyra':
                    page.component.startsWith('admin/'),
            }"
            :data-admin-lyra="
                page.component.startsWith('admin/') ? '' : undefined
            "
            :dir="page.props.localization.direction"
            :default-open="isOpen"
        >
            <slot />
        </SidebarProvider>
    </ConfigProvider>
</template>
