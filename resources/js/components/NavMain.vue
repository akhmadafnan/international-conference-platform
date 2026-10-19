<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { t } = useI18n();
const { isCurrentOrParentUrl, isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>
            {{ t('navigation.platform') }}
        </SidebarGroupLabel>

        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="
                        item.href === '/dashboard'
                            ? isCurrentUrl(item.href)
                            : isCurrentOrParentUrl(item.href)
                    "
                    :tooltip="item.title"
                >
                    <Link
                        :href="item.href"
                        :aria-current="
                            (
                                item.href === '/dashboard'
                                    ? isCurrentUrl(item.href)
                                    : isCurrentOrParentUrl(item.href)
                            )
                                ? 'page'
                                : undefined
                        "
                    >
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
