import Image from "next/image";
import Link from "next/link";
import { ArrowRight } from "lucide-react";

import { Background } from "@/components/background";
import { DashedLine } from "@/components/dashed-line";
import { Button } from "@/components/ui/button";

type Service = "software" | "automation";

const content = {
  software: {
    eyebrow: "SOFTWARE EMPRESARIAL",
    title: "Un sistema que entiende cómo trabaja tu empresa.",
    lead: "Diseñamos plataformas, portales y aplicaciones a la medida cuando las herramientas genéricas ya no alcanzan para tu operación.",
    image: "/about/4.webp",
    steps: ["Entendemos el proceso y sus excepciones", "Definimos una arquitectura que pueda crecer", "Construimos, integramos y ponemos en producción"],
  },
  automation: {
    eyebrow: "AUTOMATIZACIÓN E INTEGRACIONES",
    title: "Menos captura manual. Más operación conectada.",
    lead: "Conectamos ERP, CRM, proveedores y herramientas internas para que la información llegue al lugar correcto sin depender de tareas repetitivas.",
    image: "/resource-allocation/discussions.webp",
    steps: ["Mapeamos el flujo que hoy genera fricción", "Conectamos sistemas y validamos la información", "Automatizamos con trazabilidad y control"],
  },
} as const;

export function ServiceDetail({ service }: { service: Service }) {
  const page = content[service];

  return <Background>
    <section className="container py-28 lg:pt-44 lg:pb-24"><p className="text-primary font-mono text-xs font-bold tracking-[.16em]">{page.eyebrow}</p><div className="mt-7 grid gap-10 lg:grid-cols-2 lg:items-end"><h1 className="max-w-3xl text-4xl tracking-tight md:text-6xl lg:text-7xl">{page.title}</h1><p className="text-muted-foreground max-w-xl text-lg leading-relaxed md:text-xl">{page.lead}</p></div><Button size="lg" className="mt-10" asChild><Link href="/contact">Hablemos de tu operación <ArrowRight /></Link></Button></section>
    <div className="container"><Image src={page.image} alt={page.title} width={1600} height={820} className="aspect-[2/1] w-full rounded-2xl border object-cover" /></div>
    <DashedLine className="container my-20 lg:my-28" />
    <section className="container pb-28 lg:pb-32"><div className="grid gap-10 lg:grid-cols-[.8fr_1.2fr]"><div><p className="text-primary font-mono text-xs font-bold tracking-[.16em]">CÓMO LO ABORDAMOS</p><h2 className="mt-5 text-3xl tracking-tight md:text-5xl">Tecnología con propósito operativo.</h2></div><ol className="border-t">{page.steps.map((step, index) => <li key={step} className="grid grid-cols-[4rem_1fr] border-b py-7 md:py-9"><span className="text-primary font-mono">0{index + 1}</span><span className="text-xl font-medium md:text-2xl">{step}</span></li>)}</ol></div></section>
  </Background>;
}
