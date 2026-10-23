<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { PhDownloadSimple } from '@phosphor-icons/vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import type { AcademicPacket } from '@/types/academic';
defineProps<{ packet: AcademicPacket }>();
const { t } = useI18n();
</script>
<template>
    <Card class="w-full min-w-0 [overflow-wrap:anywhere]"
        ><CardHeader
            ><CardTitle dir="auto" class="text-lg leading-snug">{{
                packet.title
            }}</CardTitle>
            <p class="text-sm text-muted-foreground">
                <bdi>{{ packet.paperCode }}</bdi>
                <span aria-hidden="true">·</span>
                <bdi>{{ packet.edition }}</bdi>
            </p></CardHeader
        >
        <CardContent class="min-w-0 space-y-5">
            <p
                class="w-full min-w-0 text-sm leading-7 [overflow-wrap:anywhere] whitespace-pre-wrap"
                :dir="packet.locale === 'ar' ? 'rtl' : 'ltr'"
            >
                {{ packet.abstract }}
            </p>
            <div class="flex min-w-0 flex-wrap gap-2">
                <Badge
                    v-for="keyword in packet.keywords"
                    :key="keyword"
                    variant="secondary"
                    class="max-w-full text-start whitespace-normal"
                    ><bdi>{{ keyword }}</bdi></Badge
                >
            </div>
            <Separator />
            <section>
                <h2 class="font-semibold">{{ t('academic.authors') }}</h2>
                <ol
                    class="mt-3 grid min-w-0 gap-4 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <li v-for="author in packet.authors" :key="author.id">
                        <bdi>{{ author.display_name }}</bdi>
                        <p class="text-sm text-muted-foreground">
                            <bdi>{{ author.email }}</bdi>
                        </p>
                        <p
                            v-for="(aff, i) in author.affiliations"
                            :key="i"
                            class="text-sm"
                        >
                            <bdi>{{ aff.institution_name }}</bdi>
                            <bdi>{{ aff.country_code }}</bdi>
                        </p>
                    </li>
                </ol>
            </section>
            <section v-if="packet.references.length" class="min-w-0 space-y-4">
                <Separator />
                <h2 class="font-semibold">{{ t('academic.references') }}</h2>
                <ol
                    class="mt-3 w-full min-w-0 list-inside list-decimal space-y-3 text-sm leading-6 [overflow-wrap:anywhere]"
                >
                    <li
                        v-for="reference in packet.references"
                        :key="reference.id"
                        dir="auto"
                    >
                        {{ reference.raw_citation }}
                    </li>
                </ol>
            </section>
            <Separator v-if="packet.fileUrl" />
            <Button
                v-if="packet.fileUrl"
                as-child
                variant="outline"
                class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                ><a :href="packet.fileUrl"
                    ><PhDownloadSimple class="size-4" aria-hidden="true" />{{
                        t('academic.downloadAbstract')
                    }}</a
                ></Button
            >
        </CardContent>
    </Card>
</template>
