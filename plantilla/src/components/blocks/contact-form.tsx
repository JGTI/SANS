"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { Check } from "lucide-react";
import { motion } from "motion/react";
import { useState } from "react";
import { useForm } from "react-hook-form";
import * as z from "zod";

import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";

const formSchema = z.object({
  name: z.string().trim().min(2, "Escribe tu nombre."),
  email: z.string().trim().email("Escribe un correo válido."),
  company: z.string().trim().max(160).optional(),
  phone: z.string().trim().max(40).optional(),
  service: z.string().optional(),
  message: z.string().trim().min(10, "Cuéntanos un poco más.").max(5000),
  agree: z.literal(true, { message: "Necesitamos tu autorización para responderte." }),
  website: z.string().max(0).optional(),
});

type Schema = z.infer<typeof formSchema>;

declare global {
  interface Window {
    grecaptcha?: {
      ready: (callback: () => void) => void;
      execute: (siteKey: string, options: { action: string }) => Promise<string>;
    };
  }
}

const loadRecaptcha = (siteKey: string) => new Promise<void>((resolve, reject) => {
  if (window.grecaptcha) {
    window.grecaptcha.ready(resolve);
    return;
  }

  const script = document.createElement("script");
  script.src = `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(siteKey)}`;
  script.async = true;
  script.onload = () => window.grecaptcha?.ready(resolve);
  script.onerror = () => reject(new Error("No fue posible cargar la verificación de seguridad."));
  document.head.appendChild(script);
});

const getRecaptchaToken = async () => {
  const siteKey = document.querySelector<HTMLMetaElement>('meta[name="recaptcha-site-key"]')?.content;
  if (!siteKey) {
    throw new Error("El formulario no está disponible temporalmente.");
  }

  await loadRecaptcha(siteKey);
  if (!window.grecaptcha) {
    throw new Error("No fue posible verificar tu solicitud. Intenta nuevamente.");
  }

  return window.grecaptcha.execute(siteKey, { action: "contact" });
};

export function ContactForm() {
  const [status, setStatus] = useState<"idle" | "sending" | "success" | "error">("idle");
  const [error, setError] = useState("");
  const form = useForm<Schema>({
    resolver: zodResolver(formSchema),
    defaultValues: { name: "", email: "", company: "", phone: "", service: "", message: "", agree: false, website: "" },
  });

  const handleSubmit = form.handleSubmit(async (data) => {
    setStatus("sending");
    setError("");

    try {
      const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;
      const appBase = document.querySelector<HTMLMetaElement>('meta[name="app-base"]')?.content.replace(/\/$/, "") ?? "";
      const recaptchaToken = await getRecaptchaToken();
      const response = await fetch(`${appBase}/contact`, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/json", Accept: "application/json", ...(csrf ? { "X-CSRF-TOKEN": csrf } : {}) },
        body: JSON.stringify({ ...data, recaptcha_token: recaptchaToken }),
      });
      const result = await response.json().catch(() => ({}));

      if (!response.ok) {
        throw new Error(result.message || "No pudimos enviar tu mensaje. Intenta de nuevo.");
      }

      form.reset();
      setStatus("success");
    } catch (exception) {
      setError(exception instanceof Error ? exception.message : "No pudimos enviar tu mensaje. Intenta de nuevo.");
      setStatus("error");
    }
  });

  if (status === "success") {
    return <div className="w-full rounded-md border p-2 sm:p-5 md:p-8"><motion.div initial={{ opacity: 0, y: -16 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }} className="px-3 py-6"><div className="mx-auto mb-4 flex w-fit justify-center rounded-full border p-2"><Check className="size-8" /></div><h2 className="mb-2 text-center text-2xl font-bold">Gracias por escribirnos</h2><p className="text-muted-foreground text-center text-lg">Recibimos tu mensaje. Un integrante de NEXUS se pondrá en contacto contigo pronto.</p></motion.div></div>;
  }

  return <Form {...form}><form onSubmit={handleSubmit} className="flex w-full flex-col gap-2 space-y-4 rounded-md">
    <input {...form.register("website")} className="hidden" tabIndex={-1} autoComplete="off" aria-hidden="true" />
    <FormField control={form.control} name="name" render={({ field }) => <FormItem><FormLabel>Nombre completo *</FormLabel><FormControl><Input {...field} placeholder="Tu nombre" /></FormControl><FormMessage /></FormItem>} />
    <FormField control={form.control} name="email" render={({ field }) => <FormItem><FormLabel>Correo electrónico *</FormLabel><FormControl><Input {...field} type="email" placeholder="tu@empresa.com" /></FormControl><FormMessage /></FormItem>} />
    <div className="grid gap-4 sm:grid-cols-2">
      <FormField control={form.control} name="company" render={({ field }) => <FormItem><FormLabel>Empresa</FormLabel><FormControl><Input {...field} placeholder="Nombre de tu empresa" /></FormControl><FormMessage /></FormItem>} />
      <FormField control={form.control} name="phone" render={({ field }) => <FormItem><FormLabel>Teléfono</FormLabel><FormControl><Input {...field} type="tel" placeholder="Opcional" /></FormControl><FormMessage /></FormItem>} />
    </div>
    <FormField control={form.control} name="service" render={({ field }) => <FormItem><FormLabel>¿Qué necesitas resolver?</FormLabel><Select onValueChange={field.onChange} value={field.value}><FormControl><SelectTrigger><SelectValue placeholder="Selecciona una opción" /></SelectTrigger></FormControl><SelectContent>{["Software empresarial", "Automatización", "Integraciones", "Datos & BI", "Modernización", "Infraestructura", "No estoy seguro"].map((service) => <SelectItem key={service} value={service}>{service}</SelectItem>)}</SelectContent></Select><FormMessage /></FormItem>} />
    <FormField control={form.control} name="message" render={({ field }) => <FormItem><FormLabel>Cuéntanos del reto *</FormLabel><FormControl><Textarea {...field} placeholder="¿Qué está pasando hoy y qué te gustaría mejorar?" className="resize-none" rows={5} /></FormControl><FormMessage /></FormItem>} />
    <FormField control={form.control} name="agree" render={({ field }) => <FormItem className="flex flex-row items-start space-x-2 space-y-0"><FormControl><Checkbox checked={field.value} onCheckedChange={field.onChange} /></FormControl><div className="space-y-1 leading-none"><FormLabel>Autorizo el tratamiento de mis datos para atender esta solicitud.</FormLabel><FormMessage /></div></FormItem>} />
    {status === "error" && <p className="text-destructive text-sm">{error}</p>}
    <div className="flex w-full items-center justify-end pt-3"><Button className="rounded-lg" size="sm" type="submit" disabled={status === "sending"}>{status === "sending" ? "Enviando..." : "Enviar mensaje"}</Button></div>
  </form></Form>;
}
