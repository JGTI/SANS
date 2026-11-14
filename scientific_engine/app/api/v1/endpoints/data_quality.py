"""
SPACE VISION Data Quality Observatory Endpoint
"""
from fastapi import APIRouter, Depends
from pydantic import BaseModel
from typing import List, Dict, Any
from app.data_quality.validator import audit_dataset_quality
from app.core.security import verify_internal_token

router = APIRouter(dependencies=[Depends(verify_internal_token)])

class AuditDatasetRequest(BaseModel):
    records: List[Dict[str, Any]]

@router.post("/audit", summary="Audita la completitud, límites biológicos y calidad de un conjunto de datos")
def audit_data(req: AuditDatasetRequest):
    return audit_dataset_quality(req.records)
