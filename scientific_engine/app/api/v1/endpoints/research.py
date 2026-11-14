"""
SPACE VISION NASA Research Intelligence Endpoint
"""
from fastapi import APIRouter, Depends, Query
from pydantic import BaseModel, Field
from app.research_intelligence.semantic_search import ResearchIntelligenceEngine
from app.core.security import verify_internal_token

router = APIRouter(dependencies=[Depends(verify_internal_token)])
engine = ResearchIntelligenceEngine()

class SemanticSearchRequest(BaseModel):
    query: str = Field(..., min_length=2, description="Término o hipótesis biomédica aeroespacial")
    top_k: int = Field(3, ge=1, le=10)

@router.post("/semantic-search", summary="Búsqueda semántica sobre corpus indexado de NASA NTRS y OSDR")
def search_literature(req: SemanticSearchRequest):
    return engine.search(query=req.query, top_k=req.top_k)

@router.get("/semantic-search", summary="Búsqueda semántica rápida por query param")
def search_literature_get(q: str = Query(..., min_length=2), limit: int = Query(3, ge=1, le=10)):
    return engine.search(query=q, top_k=limit)
