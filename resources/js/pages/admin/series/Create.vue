<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const { t, te } = useI18n();
const languages = ['id', 'en', 'ar'] as const;
const form = useForm({
    code: '',
    name: '',
    acronym: '',
    about_i18n: { id: '', en: '', ar: '' },
});
const errorMessage = (message: string | undefined) => {
    if (!message) return undefined;
    const key = `admin.series.errors.${message}`;
    return te(key) ? t(key) : t('admin.series.errors.invalid');
};
const submit = () => form.post('/admin/series');
</script>

<template>
    <Head :title="t('admin.series.create')" />

    <div class="flex w-full max-w-3xl flex-col gap-6 p-4 md:p-8">
        <header>
            <h1 class="text-2xl font-semibold tracking-tight">
                {{ t('admin.series.create') }}
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ t('admin.series.createDescription') }}
            </p>
        </header>

        <Card>
            <CardContent class="pt-6">
                <form class="space-y-6" @submit.prevent="submit">
                    <p
                        v-if="form.hasErrors"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ t('admin.series.errors.summary') }}
                    </p>
                    <div class="grid gap-2">
                        <Label for="series-code">{{
                            t('admin.series.code')
                        }}</Label>
                        <Input
                            id="series-code"
                            v-model="form.code"
                            name="code"
                            dir="ltr"
                            required
                            maxlength="255"
                            :aria-invalid="!!form.errors.code"
                            aria-describedby="series-code-help series-code-error"
                        />
                        <p
                            id="series-code-help"
                            class="text-sm text-muted-foreground"
                        >
                            {{ t('admin.series.codeHelp') }}
                        </p>
                        <InputError
                            id="series-code-error"
                            :message="errorMessage(form.errors.code)"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="series-name">{{
                            t('admin.series.name')
                        }}</Label>
                        <Input
                            id="series-name"
                            v-model="form.name"
                            name="name"
                            dir="auto"
                            required
                            maxlength="255"
                            :aria-invalid="!!form.errors.name"
                            aria-describedby="series-name-error"
                        />
                        <InputError
                            id="series-name-error"
                            :message="errorMessage(form.errors.name)"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="series-acronym"
                            >{{ t('admin.series.acronym') }} ({{
                                t('admin.series.optional')
                            }})</Label
                        >
                        <Input
                            id="series-acronym"
                            v-model="form.acronym"
                            name="acronym"
                            dir="auto"
                            maxlength="255"
                            :aria-invalid="!!form.errors.acronym"
                            aria-describedby="series-acronym-error"
                        />
                        <InputError
                            id="series-acronym-error"
                            :message="errorMessage(form.errors.acronym)"
                        />
                    </div>
                    <fieldset class="space-y-4">
                        <legend class="mb-2 text-sm font-medium">
                            {{ t('admin.series.about') }} ({{
                                t('admin.series.optional')
                            }})
                        </legend>
                        <InputError
                            :message="errorMessage(form.errors.about_i18n)"
                        />
                        <div
                            v-for="language in languages"
                            :key="language"
                            class="grid gap-2"
                        >
                            <Label :for="`series-about-${language}`">{{
                                t(`admin.series.languages.${language}`)
                            }}</Label>
                            <Textarea
                                :id="`series-about-${language}`"
                                v-model="form.about_i18n[language]"
                                :name="`about_i18n[${language}]`"
                                :lang="language"
                                :dir="language === 'ar' ? 'rtl' : 'ltr'"
                                rows="4"
                                maxlength="10000"
                                class="w-full"
                                :aria-invalid="
                                    !!form.errors[`about_i18n.${language}`]
                                "
                                :aria-describedby="`series-about-${language}-error`"
                            />
                            <InputError
                                :id="`series-about-${language}-error`"
                                :message="
                                    errorMessage(
                                        form.errors[`about_i18n.${language}`],
                                    )
                                "
                            />
                        </div>
                    </fieldset>
                    <div class="flex flex-wrap gap-3">
                        <Button type="submit" :disabled="form.processing">
                            {{
                                t(
                                    form.processing
                                        ? 'admin.series.saving'
                                        : 'admin.series.save',
                                )
                            }}
                        </Button>
                        <Button as-child variant="outline"
                            ><Link href="/admin/series">{{
                                t('admin.series.cancel')
                            }}</Link></Button
                        >
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
