<script setup lang="ts">
import { Moon, Sun } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/composables/useAppearance';

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
        <Sun v-if="resolvedAppearance === 'dark'" class="size-4" />

        <Moon v-else class="size-4" />

        <span class="sr-only">
            {{
                resolvedAppearance === 'dark'
                    ? t('appearance.switchToLight')
                    : t('appearance.switchToDark')
            }}
        </span>
    </Button>
</template>
