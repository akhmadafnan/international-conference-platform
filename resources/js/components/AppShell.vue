<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
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
    <div
        v-if="variant === 'header'"
        class="workspace-shell flex min-h-screen w-full flex-col"
    >
        <slot />
    </div>
    <SidebarProvider
        v-else
        class="workspace-shell"
        :class="{ 'admin-workspace': page.component.startsWith('admin/') }"
        :default-open="isOpen"
    >
        <slot />
    </SidebarProvider>
</template>
