<script setup lang="ts">
import { Moon, Sun } from '@lucide/vue';
import { PhMoon, PhSun } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/composables/useAppearance';

defineProps<{ adminPilot?: boolean }>();

const { resolvedAppearance, updateAppearance } = useAppearance();

const { t } = useI18n();

function toggleAppearance(): void {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <Button
        type="button"
        variant="ghost"
        size="icon-sm"
        :aria-label="
            resolvedAppearance === 'dark'
                ? t('appearance.switchToLight')
                : t('appearance.switchToDark')
        "
        @click="toggleAppearance"
    >
        <component
            :is="
                resolvedAppearance === 'dark'
                    ? adminPilot
                        ? PhSun
                        : Sun
                    : adminPilot
                      ? PhMoon
                      : Moon
            "
            class="size-4"
            aria-hidden="true"
        />

        <span class="sr-only">
            {{
                resolvedAppearance === 'dark'
                    ? t('appearance.switchToLight')
                    : t('appearance.switchToDark')
            }}
        </span>
    </Button>
</template>
