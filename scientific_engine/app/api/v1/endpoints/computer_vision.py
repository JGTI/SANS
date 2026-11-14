"""
SPACE VISION Computer Vision & OCT Endpoints
"""
from fastapi import APIRouter, Depends
from pydantic import BaseModel, Field
from app.computer_vision.oct_preprocessor import OctImagePreprocessor
from app.core.security import verify_internal_token

router = APIRouter(dependencies=[Depends(verify_internal_token)])

class OctProfileRequest(BaseModel):
    base_thickness_microns: float = Field(95.0, ge=40.0, le=200.0)
    edema_delta_microns: float = Field(0.0, ge=-20.0, le=100.0)
    sectors_count: int = Field(12, ge=4, le=48)

class ValidateImageRequest(BaseModel):
    width: int
    height: int
    channels: int = 1
    bit_depth: int = 8
    modality: str = "OCT_B_SCAN"

class CirrusOctRequest(BaseModel):
    astronaut_code: str = Field("ASTRO-01", description="Código del astronauta")
    astronaut_name: str = Field("CDR Alex Mercer", description="Nombre del tripulante")
    flight_day: int = Field(45, ge=0, le=730, description="Día de vuelo en microgravedad")
    scenario: str = Field("B", description="Escenario de simulación (A, B, C, D, E)")

@router.post("/oct-profile", summary="Genera perfil peripapilar TSNIT de grosor retiniano RNFL")
def generate_oct_profile(req: OctProfileRequest):
    return OctImagePreprocessor.extract_simulated_rnfl_profile(
        base_thickness_microns=req.base_thickness_microns,
        edema_delta_microns=req.edema_delta_microns,
        sectors_count=req.sectors_count
    )

@router.post("/cirrus-workstation", summary="Emulación de consola tomográfica ZEISS Cirrus HD-OCT Macular Cube 512x128")
def get_cirrus_oct_workstation(req: CirrusOctRequest):
    return OctImagePreprocessor.generate_cirrus_oct_workstation_data(
        astronaut_code=req.astronaut_code,
        astronaut_name=req.astronaut_name,
        flight_day=req.flight_day,
        scenario=req.scenario
    )

@router.post("/validate-image", summary="Valida resolución y metadatos de imágenes OCT o fondo de ojo")
def validate_image(req: ValidateImageRequest):
    return OctImagePreprocessor.validate_image_metadata(
        width=req.width,
        height=req.height,
        channels=req.channels,
        bit_depth=req.bit_depth,
        modality=req.modality
    )
