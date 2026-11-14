import Image from "next/image";

import { ArrowRight } from "lucide-react";

import { DashedLine } from "../dashed-line";

import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from "@/components/ui/carousel";
import { cn } from "@/lib/utils";

const items = [
  {
    quote: "Software empresarial construido alrededor de los procesos reales de tu empresa.",
    author: "Software empresarial",
    role: "Sistemas a la medida",
    company: "ERP · CRM · Portales · Operación",
    image: "/testimonials/amy-chase.webp",
  },
  {
    quote: "Automatizamos tareas repetitivas para reducir errores y tiempos operativos.",
    author: "Automatización",
    role: "Procesos empresariales",
    company: "Flujos · Reportes · Documentos",
    image: "/testimonials/jonas-kotara.webp",
  },
  {
    quote: "Conectamos sistemas para evitar captura duplicada e información aislada.",
    author: "Integraciones",
    role: "Sistemas conectados",
    company: "APIs · Webhooks · Sincronización",
    image: "/testimonials/kevin-yam.webp",
  },
  {
    quote: "Transformamos datos dispersos en información útil para operar y decidir.",
    author: "Datos & BI",
    role: "Información accionable",
    company: "Bases de datos · Dashboards · ETL",
    image: "/testimonials/kundo-marta.webp",
  },
  {
    quote: "Evolucionamos software y procesos que ya no responden a la operación actual.",
    author: "Modernización",
    role: "Sistemas preparados para crecer",
    company: "Migración · Refactorización · Legacy",
    image: "/testimonials/amy-chase.webp",
  },
  {
    quote: "Infraestructura diseñada para mantener sistemas disponibles, seguros y escalables.",
    author: "Infraestructura & DevOps",
    role: "Operación confiable",
    company: "Linux · CI/CD · Backups · Monitoreo",
    image: "/testimonials/jonas-kotara.webp",
  },
  {
    quote: "Comenzamos entendiendo el proceso antes de proponer una herramienta.",
    author: "Consultoría tecnológica",
    role: "Decisiones con contexto",
    company: "Diagnóstico · Arquitectura · Roadmap",
    image: "/testimonials/kevin-yam.webp",
  },
  {
    quote: "Tu proyecto se atiende con desarrollo, datos e infraestructura como un solo equipo.",
    author: "NEXUS DIGITAL DB",
    role: "5 ingenieros",
    company: "Capacidad técnica de punta a punta",
    image: "/testimonials/kundo-marta.webp",
  },
];

export const Testimonials = ({
  className,
  dashedLineClassName,
}: {
  className?: string;
  dashedLineClassName?: string;
}) => {
  return (
    <>
      <section className={cn("overflow-hidden py-28 lg:py-32", className)}>
        <div className="container">
          <div className="space-y-4">
            <h2 className="text-2xl tracking-tight md:text-4xl lg:text-5xl">
              Capacidad técnica para proyectos empresariales
            </h2>
            <p className="text-muted-foreground max-w-md leading-snug">
              Diseñamos, desarrollamos, integramos y mantenemos soluciones para
              operaciones que necesitan estructura, trazabilidad y escalabilidad.
            </p>
            <Button variant="outline" className="shadow-md">
              Conoce nuestras soluciones <ArrowRight className="size-4" />
            </Button>
          </div>

          <div className="relative mt-8 -mr-[max(3rem,calc((100vw-80rem)/2+3rem))] md:mt-12 lg:mt-20">
            <Carousel
              opts={{
                align: "start",
                loop: true,
              }}
              className="w-full"
            >
              <CarouselContent className="">
                {items.map((testimonial, index) => (
                  <CarouselItem
                    key={index}
                    className="xl:basis-1/3.5 grow basis-4/5 sm:basis-3/5 md:basis-2/5 lg:basis-[28%] 2xl:basis-[24%]"
                  >
                    <Card className="bg-muted h-full overflow-hidden border-none">
                      <CardContent className="flex h-full flex-col p-0">
                        <div className="relative h-[288px] lg:h-[328px]">
                          <Image
                            src={testimonial.image}
                            alt={`Capacidad NEXUS: ${testimonial.author}`}
                            fill
                            className="object-cover object-top"
                          />
                        </div>
                        <div className="flex flex-1 flex-col justify-between gap-10 p-6">
                          <blockquote className="font-display text-lg leading-none! font-medium md:text-xl lg:text-2xl">
                            {testimonial.quote}
                          </blockquote>
                          <div className="space-y-0.5">
                            <div className="text-primary font-semibold">
                              {testimonial.author}, {testimonial.role}
                            </div>
                            <div className="text-muted-foreground text-sm">
                              {testimonial.company}
                            </div>
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  </CarouselItem>
                ))}
              </CarouselContent>
              <div className="mt-8 flex gap-3">
                <CarouselPrevious className="bg-muted hover:bg-muted/80 static size-14.5 translate-x-0 translate-y-0 transition-colors [&>svg]:size-6 lg:[&>svg]:size-8" />
                <CarouselNext className="bg-muted hover:bg-muted/80 static size-14.5 translate-x-0 translate-y-0 transition-colors [&>svg]:size-6 lg:[&>svg]:size-8" />
              </div>
            </Carousel>
          </div>
        </div>
      </section>
      <DashedLine
        orientation="horizontal"
        className={cn("mx-auto max-w-[80%]", dashedLineClassName)}
      />
    </>
  );
};
