<script setup lang="ts">
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { wordCount, academicUrl } from '@/lib/submissionInputs';

const props = defineProps<{
    modelValue: string;
    disabled?: boolean;
    dir: 'rtl' | 'ltr';
    id: string;
    invalid?: boolean;
}>();
const emit = defineEmits<{ 'update:modelValue': [value: string] }>();
const { t } = useI18n();
const root = ref<HTMLElement>();
const link = ref('');
const showLink = ref(false);
const invalidLink = ref(false);
const count = computed(() => wordCount(props.modelValue));
let selection = [0, 0];
function remember() {
    const area = root.value?.querySelector('textarea');
    if (area) selection = [area.selectionStart, area.selectionEnd];
}
function insert(text: string) {
    if (props.disabled) return;
    emit(
        'update:modelValue',
        props.modelValue.slice(0, selection[0]) +
            text +
            props.modelValue.slice(selection[1]),
    );
}
function script(superscript: boolean) {
    const source = '0123456789+-=()';
    const target = superscript ? '⁰¹²³⁴⁵⁶⁷⁸⁹⁺⁻⁼⁽⁾' : '₀₁₂₃₄₅₆₇₈₉₊₋₌₍₎';
    const text = props.modelValue.slice(selection[0], selection[1]);
    insert(
        [...text]
            .map((letter) =>
                source.includes(letter)
                    ? target[source.indexOf(letter)]
                    : letter,
            )
            .join(''),
    );
}
function insertLink() {
    try {
        const url = academicUrl(link.value);
        if (!url) throw new Error();
        insert(url);
        showLink.value = false;
        link.value = '';
        invalidLink.value = false;
    } catch {
        invalidLink.value = true;
    }
}
</script>
<template>
    <div ref="root" class="grid gap-2">
        <div
            role="group"
            :aria-label="t('wizardUx.editorTools')"
            class="flex flex-wrap gap-1 border border-border bg-muted/40 p-2"
        >
            <Button
                type="button"
                size="sm"
                variant="ghost"
                disabled
                :title="t('wizardUx.plainText')"
                ><strong>B</strong></Button
            >
            <Button
                type="button"
                size="sm"
                variant="ghost"
                disabled
                :title="t('wizardUx.plainText')"
                ><em>I</em></Button
            >
            <Button
                type="button"
                size="sm"
                variant="outline"
                :disabled="disabled"
                :aria-label="t('wizardUx.superscript')"
                @click="script(true)"
                >x²</Button
            >
            <Button
                type="button"
                size="sm"
                variant="outline"
                :disabled="disabled"
                :aria-label="t('wizardUx.subscript')"
                @click="script(false)"
                >x₂</Button
            >
            <Button
                type="button"
                size="sm"
                variant="outline"
                :disabled="disabled"
                @click="showLink = !showLink"
                >{{ t('wizardUx.insertLink') }}</Button
            >
        </div>
        <div v-if="showLink" class="flex flex-wrap items-center gap-2">
            <Input
                v-model="link"
                type="url"
                dir="ltr"
                :aria-label="t('wizardUx.insertLink')"
                placeholder="https://"
                :disabled="disabled"
                @keydown.enter.prevent="insertLink"
            />
            <Button
                type="button"
                variant="outline"
                :disabled="disabled"
                @click="insertLink"
                >{{ t('wizardUx.insertLink') }}</Button
            >
            <p v-if="invalidLink" role="alert" class="text-xs text-destructive">
                {{ t('wizardUx.invalidLink') }}
            </p>
        </div>
        <Textarea
            :id="id"
            :model-value="modelValue"
            :dir="dir"
            :disabled="disabled"
            required
            maxlength="30000"
            rows="14"
            class="min-h-72 resize-y text-sm leading-7"
            :aria-invalid="invalid"
            :aria-describedby="`${id}-editor-help ${id}-error`"
            @update:model-value="
                (value) => emit('update:modelValue', String(value))
            "
            @select="remember"
            @keyup="remember"
            @click="remember"
            @blur="remember"
        />
        <p :id="`${id}-editor-help`" class="text-xs text-muted-foreground">
            {{ t('wizardUx.plainText') }}
        </p>
        <p role="status" class="text-xs text-muted-foreground">
            {{ t('wizardUx.wordCount', { count }) }}
        </p>
    </div>
</template>
