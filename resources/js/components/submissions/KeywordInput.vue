<script setup lang="ts">
import { computed, ref } from 'vue';
import { PhX } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { keywordTags, serializeKeywords } from '@/lib/submissionInputs';

const props = defineProps<{
    modelValue: string;
    disabled?: boolean;
    min: number | null;
    max: number | null;
    id: string;
}>();
const emit = defineEmits<{ 'update:modelValue': [value: string] }>();
const { t } = useI18n();
const pending = ref('');
const tags = computed(() => keywordTags(props.modelValue));
function commit() {
    if (props.disabled) return;
    emit(
        'update:modelValue',
        serializeKeywords([...tags.value, ...keywordTags(pending.value)]),
    );
    pending.value = '';
}
function remove(index: number) {
    if (!props.disabled)
        emit(
            'update:modelValue',
            serializeKeywords(tags.value.filter((_, i) => i !== index)),
        );
}
function keydown(event: KeyboardEvent) {
    if (event.isComposing) return;
    if (['Enter', ',', '،'].includes(event.key)) {
        event.preventDefault();
        commit();
    } else if (event.key === 'Backspace' && !pending.value)
        remove(tags.value.length - 1);
}
function paste(event: ClipboardEvent) {
    const text = event.clipboardData?.getData('text/plain');
    if (!text || !/[,،\r\n]/u.test(text)) return;
    event.preventDefault();
    pending.value += text;
    commit();
}
defineExpose({ commit });
</script>
<template>
    <div class="grid gap-2">
        <div class="flex flex-wrap gap-2" :aria-label="t('wizardUx.keywords')">
            <Badge
                v-for="(tag, index) in tags"
                :key="tag"
                variant="secondary"
                class="max-w-full gap-1 whitespace-normal"
            >
                <bdi class="break-words">{{ tag }}</bdi>
                <Button
                    type="button"
                    size="icon-sm"
                    variant="ghost"
                    :disabled="disabled"
                    :aria-label="t('wizardUx.removeKeyword', { keyword: tag })"
                    @click="remove(index)"
                    ><PhX class="size-3" aria-hidden="true"
                /></Button>
            </Badge>
        </div>
        <Input
            :id="id"
            v-model="pending"
            :disabled="disabled"
            :placeholder="t('wizardUx.keywordHint')"
            :aria-describedby="`${id}-hint`"
            @keydown="keydown"
            @paste="paste"
            @blur="commit"
        />
        <p
            :id="`${id}-hint`"
            class="text-xs text-muted-foreground"
            aria-live="polite"
        >
            {{ t('wizardUx.keywordCount', { count: tags.length })
            }}<span v-if="min !== null && max !== null">
                · {{ t('wizardUx.keywordRange', { min, max }) }}</span
            >
        </p>
    </div>
</template>
