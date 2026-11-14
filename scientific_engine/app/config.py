"""
SPACE VISION Scientific Engine - Configuration
"""
import os
from pydantic import BaseModel

class Settings(BaseModel):
    app_name: str = "SPACE VISION Scientific Computing & ML Engine"
    app_version: str = "1.0.0"
    app_env: str = os.getenv("APP_ENV", "development")
    host: str = os.getenv("SCIENTIFIC_ENGINE_HOST", "127.0.0.1")
    port: int = int(os.getenv("SCIENTIFIC_ENGINE_PORT", "8001"))
    secret_token: str = os.getenv("SCIENTIFIC_ENGINE_TOKEN", "spacevision-scientific-secret-token-2026")
    data_dir: str = os.getenv("SCIENTIFIC_ENGINE_DATA_DIR", os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "data")))
    cors_origins: list[str] = ["*"]

settings = Settings()
