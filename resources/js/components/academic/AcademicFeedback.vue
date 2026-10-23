<script setup lang="ts">
import { useI18n } from 'vue-i18n';
defineProps<{ errors?: Record<string, string>; saved?: boolean }>();
const { t, te } = useI18n();
</script>
<template>
    <p
        v-if="saved"
        role="status"
        class="rounded-lg border border-success/30 bg-success/10 p-4 text-sm [overflow-wrap:anywhere] text-success"
    >
        {{ t('academic.saved') }}
    </p>
    <ul
        v-if="errors && Object.keys(errors).length"
        role="alert"
        class="list-inside list-disc space-y-1 rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-sm [overflow-wrap:anywhere] text-destructive"
    >
        <li v-for="(message, field) in errors" :key="field">
            {{
                te('academic.errors.' + message)
                    ? t('academic.errors.' + message)
                    : t('academic.errors.validation')
            }}
        </li>
    </ul>
</template>
