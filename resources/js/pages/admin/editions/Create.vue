<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';

defineProps<{ series: { id: string; code: string; name: string }[] }>();
const { t, te } = useI18n();
const languages = ['id', 'en', 'ar'] as const;
const modes = ['OFFLINE', 'ONLINE', 'HYBRID'] as const;
const form = useForm({
    series_id: '',
    edition_code: '',
    edition_number: '',
    year: '',
    host_name: '',
    mode: 'OFFLINE',
    timezone: 'Asia/Jakarta',
    starts_at: '',
    ends_at: '',
    theme_i18n: { id: '', en: '', ar: '' },
    organizer_i18n: { id: '', en: '', ar: '' },
});
const fields = [
    { key: 'edition_code', type: 'text', required: true, max: 255 },
    { key: 'edition_number', type: 'number', required: false, max: 65535 },
    { key: 'year', type: 'number', required: true, max: 9999 },
    { key: 'host_name', type: 'text', required: true, max: 255 },
    { key: 'timezone', type: 'text', required: true, max: 64 },
    { key: 'starts_at', type: 'datetime-local', required: true },
    { key: 'ends_at', type: 'datetime-local', required: true },
] as const;
const localizedFields = ['theme_i18n', 'organizer_i18n'] as const;
const errorMessage = (message: string | undefined) => {
    if (!message) return undefined;
    const key = `admin.editions.errors.${message}`;
    return te(key) ? t(key) : t('admin.editions.errors.invalid');
};
const submit = () => form.post('/admin/editions');
</script>

<template>
    <Head :title="t('admin.editions.create')" />
    <div class="flex w-full max-w-4xl flex-col gap-6 p-4 md:p-8">
        <header>
            <h1 class="text-2xl font-semibold tracking-tight">
                {{ t('admin.editions.create') }}
            </h1>
            <p class="mt-2 text-sm leading-6 text-muted-foreground">
                {{ t('admin.editions.createDescription') }}
            </p>
        </header>
        <Card v-if="series.length === 0">
            <CardContent class="space-y-4 pt-6">
                <p role="status" class="text-sm text-muted-foreground">
                    {{ t('admin.editions.noSeries') }}
                </p>
                <Button as-child variant="outline"
                    ><Link href="/admin/series/create">{{
                        t('admin.series.create')
                    }}</Link></Button
                >
            </CardContent>
        </Card>
        <Card v-else>
            <CardContent class="pt-6">
                <form class="space-y-6" @submit.prevent="submit">
                    <p
                        v-if="form.hasErrors"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ t('admin.editions.errors.summary') }}
                    </p>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="grid gap-2 sm:col-span-2">
                            <Label for="edition-series">{{
                                t('admin.editions.series')
                            }}</Label>
                            <Select
                                v-model="form.series_id"
                                name="series_id"
                                required
                            >
                                <SelectTrigger
                                    id="edition-series"
                                    class="w-full"
                                    :aria-invalid="!!form.errors.series_id"
                                    aria-describedby="edition-series-error"
                                >
                                    <SelectValue
                                        :placeholder="
                                            t('admin.editions.selectSeries')
                                        "
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="item in series"
                                        :key="item.id"
                                        :value="item.id"
                                    >
                                        {{ item.code }} — {{ item.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError
                                id="edition-series-error"
                                :message="errorMessage(form.errors.series_id)"
                            />
                        </div>
                        <div
                            v-for="field in fields"
                            :key="field.key"
                            class="grid content-start gap-2"
                        >
                            <Label :for="`edition-${field.key}`"
                                >{{ t(`admin.editions.fields.${field.key}`)
                                }}<span v-if="!field.required">
                                    ({{ t('admin.series.optional') }})</span
                                ></Label
                            >
                            <Input
                                :id="`edition-${field.key}`"
                                v-model="form[field.key]"
                                :name="field.key"
                                :type="field.type"
                                :required="field.required"
                                :min="field.type === 'number' ? 1 : undefined"
                                :max="
                                    field.type === 'number' && 'max' in field
                                        ? field.max
                                        : undefined
                                "
                                :maxlength="
                                    field.type === 'text' && 'max' in field
                                        ? field.max
                                        : undefined
                                "
                                :dir="
                                    field.key === 'host_name' ? 'auto' : 'ltr'
                                "
                                :aria-invalid="!!form.errors[field.key]"
                                :aria-describedby="`edition-${field.key}-error${field.key === 'timezone' ? ' edition-timezone-help' : ''}${field.key === 'edition_code' ? ' edition-code-help' : ''}`"
                            />
                            <p
                                v-if="field.key === 'timezone'"
                                id="edition-timezone-help"
                                class="text-xs leading-5 text-muted-foreground"
                            >
                                {{ t('admin.editions.timezoneHelp') }}
                            </p>
                            <p
                                v-if="field.key === 'edition_code'"
                                id="edition-code-help"
                                class="text-xs leading-5 text-muted-foreground"
                            >
                                {{ t('admin.editions.codeHelp') }}
                            </p>
                            <InputError
                                :id="`edition-${field.key}-error`"
                                :message="errorMessage(form.errors[field.key])"
                            />
                        </div>
                        <div class="grid content-start gap-2">
                            <Label for="edition-mode">{{
                                t('admin.editions.mode')
                            }}</Label>
                            <Select v-model="form.mode" name="mode" required>
                                <SelectTrigger
                                    id="edition-mode"
                                    class="w-full"
                                    :aria-invalid="!!form.errors.mode"
                                    aria-describedby="edition-mode-error"
                                >
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="mode in modes"
                                        :key="mode"
                                        :value="mode"
                                    >
                                        {{ t('admin.editions.modes.' + mode) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError
                                id="edition-mode-error"
                                :message="errorMessage(form.errors.mode)"
                            />
                        </div>
                    </div>
                    <fieldset
                        v-for="field in localizedFields"
                        :key="field"
                        class="space-y-4 border-t border-border pt-5"
                    >
                        <legend class="px-1 text-sm font-semibold">
                            {{ t(`admin.editions.fields.${field}`) }} ({{
                                t('admin.series.optional')
                            }})
                        </legend>
                        <InputError
                            :message="errorMessage(form.errors[field])"
                        />
                        <div class="grid gap-4 lg:grid-cols-3">
                            <div
                                v-for="language in languages"
                                :key="language"
                                class="grid gap-2"
                            >
                                <Label :for="`${field}-${language}`">{{
                                    t(`admin.series.languages.${language}`)
                                }}</Label>
                                <Textarea
                                    :id="`${field}-${language}`"
                                    v-model="form[field][language]"
                                    :name="`${field}[${language}]`"
                                    :lang="language"
                                    :dir="language === 'ar' ? 'rtl' : 'ltr'"
                                    rows="3"
                                    maxlength="10000"
                                    class="w-full"
                                    :aria-invalid="
                                        !!form.errors[`${field}.${language}`]
                                    "
                                    :aria-describedby="`${field}-${language}-error`"
                                />
                                <InputError
                                    :id="`${field}-${language}-error`"
                                    :message="
                                        errorMessage(
                                            form.errors[`${field}.${language}`],
                                        )
                                    "
                                />
                            </div>
                        </div>
                    </fieldset>
                    <div
                        class="flex flex-wrap items-center gap-3 border-t border-border pt-5"
                    >
                        <Button type="submit" :disabled="form.processing">{{
                            form.processing
                                ? t('admin.series.saving')
                                : t('admin.editions.save')
                        }}</Button>
                        <Button as-child variant="outline"
                            ><Link href="/admin/editions">{{
                                t('admin.series.cancel')
                            }}</Link></Button
                        >
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
