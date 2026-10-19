<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ConfigProvider } from 'reka-ui';
import { SidebarProvider } from '@/components/ui/sidebar';
import type { AppVariant } from '@/types';
import { useWorkspaceLyra } from '@/composables/useWorkspaceLyra';

type Props = {
    variant?: AppVariant;
};

withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});

const page = usePage();
const { isParticipant, isLyra } = useWorkspaceLyra();
const isOpen = page.props.sidebarOpen;
</script>

<template>
    <ConfigProvider
        :dir="isLyra ? page.props.localization.direction : undefined"
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
                'participant-workspace participant-lyra': isParticipant,
            }"
            :data-admin-lyra="
                page.component.startsWith('admin/') ? '' : undefined
            "
            :data-participant-lyra="isParticipant ? '' : undefined"
            :dir="page.props.localization.direction"
            :default-open="isOpen"
        >
            <slot />
        </SidebarProvider>
    </ConfigProvider>
</template>
