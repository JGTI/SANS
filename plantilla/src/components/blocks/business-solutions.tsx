import Image from "next/image";
import Link from "next/link";
import { ArrowRight } from "lucide-react";

import { Background } from "@/components/background";
import { Button } from "@/components/ui/button";
import { DashedLine } from "@/components/dashed-line";

type SolutionPageProps = { audience: "servicios" | "soluciones" | "empresas" | "pymes" };

const content = {
  servicios: {
    eyebrow: "SERVICIOS NEXUS",
    title: "Tecnología diseñada alrededor de cómo opera tu empresa.",
    lead: "Creamos software y productos digitales a la medida para resolver procesos críticos, conectar áreas y permitir que la operación escale con claridad.",
    challenge: "Un servicio útil empieza entendiendo el proceso, no eligiendo una herramienta.",
    outcomes: ["Procesos menos dependientes de una persona", "Información conectada entre áreas", "Herramientas preparadas para crecer", "Decisiones respaldadas por datos"],
    capabilities: ["Software empresarial a la medida", "Portales y plataformas B2B", "Automatización de procesos", "Integraciones entre sistemas", "Datos, dashboards y BI", "Infraestructura y DevOps"],
    promise: "No entregamos software genérico. Diseñamos tecnología que resuelve una operación concreta.",
    cta: "Cuéntanos tu reto",
  },
  soluciones: {
    eyebrow: "SOLUCIONES",
    title: "De la fricción operativa a una solución en producción.",
    lead: "Integramos sistemas existentes, eliminamos tareas repetitivas y convertimos datos dispersos en una plataforma que tu equipo puede usar todos los días.",
    challenge: "Cada empresa tiene un punto de partida distinto. La solución debe respetarlo.",
    outcomes: ["Menos trabajo manual", "Sistemas que sí se comunican", "Reportes listos para decidir", "Una base tecnológica mantenible"],
    capabilities: ["Diagnóstico y arquitectura", "Integración de ERP, CRM y APIs", "Automatización de flujos", "Modernización de sistemas legacy", "Migración y calidad de datos", "Despliegue, monitoreo y soporte"],
    promise: "Conectamos lo que ya tienes, construimos lo que falta y dejamos una base lista para crecer.",
    cta: "Analizar mi operación",
  },
  empresas: {
    eyebrow: "SOLUCIONES PARA EMPRESAS",
    title: "Tecnología para operaciones que no pueden detenerse.",
    lead: "Diseñamos software, automatización, integraciones y arquitectura de datos para empresas medianas y corporativos con procesos operativos complejos.",
    challenge: "Cuando la operación crece, los procesos aislados se vuelven un riesgo.",
    outcomes: ["Información confiable entre áreas", "Menos captura duplicada", "Reportes útiles a tiempo", "Tecnología que acompaña el crecimiento"],
    capabilities: ["Software empresarial a la medida", "Integración de ERP, CRM y proveedores", "Automatización de flujos operativos", "Datos, dashboards y Business Intelligence", "Modernización de sistemas legacy", "Infraestructura y DevOps"],
    promise: "Tu empresa no necesita más herramientas desconectadas. Necesita una solución que entienda cómo opera.",
    cta: "Hablemos de tu operación",
  },
  pymes: {
    eyebrow: "SOLUCIONES PARA PYMES",
    title: "Tecnología empresarial para PyMEs que están creciendo.",
    lead: "Digitalizamos procesos, centralizamos información y automatizamos operaciones sin obligarte a implementar plataformas innecesariamente complejas.",
    challenge: "No necesitas el sistema más grande. Necesitas el sistema correcto.",
    outcomes: ["Ventas y clientes en un solo lugar", "Menos trabajo administrativo", "Procesos más fáciles de seguir", "Una plataforma que crece contigo"],
    capabilities: ["CRM ligero y modular", "Inventarios, clientes y proveedores", "Cotizaciones y portales", "Dashboards y reportes", "Automatización administrativa", "Migración desde Excel"],
    promise: "Podemos comenzar resolviendo un proceso crítico y hacer crecer la plataforma conforme crece tu empresa.",
    cta: "Analizar mi proceso",
  },
} as const;

export function BusinessSolutions({ audience }: SolutionPageProps) {
  const page = content[audience];
  const image = audience === "pymes" ? "/about/1.webp" : "/about/3.webp";

  return (
    <Background>
      <section className="container py-28 lg:pt-44 lg:pb-28">
        <div className="grid gap-12 lg:grid-cols-[1.1fr_.9fr] lg:items-end">
          <div>
            <p className="text-primary font-mono text-xs font-bold tracking-[.16em]">{page.eyebrow}</p>
            <h1 className="mt-5 max-w-3xl text-4xl tracking-tight md:text-6xl lg:text-7xl">{page.title}</h1>
            <p className="text-muted-foreground mt-7 max-w-xl text-lg leading-relaxed md:text-xl">{page.lead}</p>
            <Button size="lg" className="mt-9" asChild><Link href="/contact">{page.cta}<ArrowRight /></Link></Button>
          </div>
          <figure className="overflow-hidden rounded-2xl border bg-card">
            <Image src={image} alt={page.title} width={900} height={700} className="aspect-[1.1/1] w-full object-cover" />
          </figure>
        </div>
      </section>

      <DashedLine className="container max-w-5xl" />

      <section className="container grid gap-12 py-20 lg:grid-cols-[.85fr_1.15fr] lg:py-28">
        <div>
          <p className="text-primary font-mono text-xs font-bold tracking-[.16em]">EL RETO</p>
          <h2 className="mt-5 max-w-lg text-3xl tracking-tight md:text-5xl">{page.challenge}</h2>
          <p className="text-muted-foreground mt-6 max-w-md leading-relaxed">Primero entendemos dónde se pierde tiempo, información o control. Después definimos un camino de implementación que sí puede operar tu equipo.</p>
        </div>
        <div className="border-t">
          {page.outcomes.map((outcome, index) => <div key={outcome} className="grid grid-cols-[3rem_1fr] border-b py-6 md:grid-cols-[4rem_1fr] md:py-8"><span className="text-primary font-mono text-sm">0{index + 1}</span><p className="text-lg font-medium md:text-2xl">{outcome}</p></div>)}
        </div>
      </section>

      <section className="container pb-20 lg:pb-28">
        <div className="border-t pt-8 md:pt-12">
          <p className="text-primary font-mono text-xs font-bold tracking-[.16em]">CAPACIDADES NEXUS</p>
          <div className="mt-5 grid gap-8 lg:grid-cols-[.85fr_1.15fr]">
            <h2 className="max-w-lg text-3xl tracking-tight md:text-5xl">Una solución alineada a tu operación.</h2>
            <div className="border-t">{page.capabilities.map((capability, index) => <div key={capability} className="flex items-baseline justify-between gap-5 border-b py-4"><span className="font-medium">{capability}</span><span className="text-muted-foreground font-mono text-xs">0{index + 1}</span></div>)}</div>
          </div>
        </div>
      </section>

      <section className="container pb-28 lg:pb-32">
        <div className="border-y py-12 md:flex md:items-end md:justify-between md:gap-10 md:py-16">
          <h2 className="max-w-3xl text-3xl tracking-tight md:text-5xl">{page.promise}</h2>
          <Button className="mt-8 shrink-0 md:mt-0" variant="outline" size="lg" asChild><Link href="/contact">{page.cta}<ArrowRight /></Link></Button>
        </div>
      </section>
    </Background>
  );
}
