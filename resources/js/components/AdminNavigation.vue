<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { PhCalendarBlank, PhSquaresFour, PhStack } from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useCurrentUrl } from '@/composables/useCurrentUrl';

const page = usePage();
const { t } = useI18n();
const { isCurrentOrParentUrl } = useCurrentUrl();
const { isMobile, state, setOpenMobile } = useSidebar();
const items = computed(() => [
    {
        title: t('admin.dashboard.title'),
        href: '/admin/dashboard',
        icon: PhSquaresFour,
    },
    { title: t('admin.series.title'), href: '/admin/series', icon: PhStack },
    {
        title: t('admin.editions.title'),
        href: '/admin/editions',
        icon: PhCalendarBlank,
    },
]);
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ t('navigation.platform') }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.href">
                <Tooltip>
                    <TooltipTrigger as-child>
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentOrParentUrl(item.href)"
                        >
                            <Link
                                :href="item.href"
                                :aria-current="
                                    isCurrentOrParentUrl(item.href)
                                        ? 'page'
                                        : undefined
                                "
                                @click="setOpenMobile(false)"
                            >
                                <component
                                    :is="item.icon"
                                    class="size-4 shrink-0"
                                    aria-hidden="true"
                                />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </TooltipTrigger>
                    <TooltipContent
                        :side="
                            page.props.localization.direction === 'rtl'
                                ? 'left'
                                : 'right'
                        "
                        :hidden="state !== 'collapsed' || isMobile"
                        >{{ item.title }}</TooltipContent
                    >
                </Tooltip>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
