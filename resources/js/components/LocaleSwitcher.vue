<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Check, Languages } from '@lucide/vue';
import { PhCheck, PhTranslate } from '@phosphor-icons/vue';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { applyClientLocale } from '@/i18n';
import type { AppLocale } from '@/types';

defineProps<{ adminPilot?: boolean }>();

const page = usePage();
const { t } = useI18n();

const processing = ref(false);

const localization = computed(() => page.props.localization);

const currentLocale = computed(() => localization.value.locale);

function selectLocale(locale: AppLocale): void {
    if (processing.value || locale === currentLocale.value) {
        return;
    }

    router.post(
        '/locale',
        { locale },
        {
            preserveScroll: true,

            onStart: () => {
                processing.value = true;
            },

            onSuccess: () => {
                applyClientLocale(locale);
            },

            onFinish: () => {
                processing.value = false;
            },
        },
    );
}
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger :as-child="true">
            <Button
                variant="ghost"
                size="sm"
                class="gap-2"
                :disabled="processing"
                :aria-label="t('locale.label')"
            >
                <component
                    :is="adminPilot ? PhTranslate : Languages"
                    class="size-4"
                    aria-hidden="true"
                />
                <span class="text-xs font-semibold uppercase">
                    {{ currentLocale }}
                </span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
            :align="localization.direction === 'rtl' ? 'start' : 'end'"
            class="min-w-48"
        >
            <DropdownMenuLabel>
                {{ t('locale.label') }}
            </DropdownMenuLabel>

            <DropdownMenuItem
                v-for="locale in localization.supportedLocales"
                :key="locale.code"
                class="cursor-pointer"
                @click="selectLocale(locale.code)"
            >
                <component
                    :is="adminPilot ? PhCheck : Check"
                    aria-hidden="true"
                    class="size-4"
                    :class="
                        locale.code === currentLocale
                            ? 'opacity-100'
                            : 'opacity-0'
                    "
                />

                <span class="flex-1" :dir="locale.direction">
                    {{ locale.label }}
                </span>

                <span class="ms-3 text-xs text-muted-foreground uppercase">
                    {{ locale.code }}
                </span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
