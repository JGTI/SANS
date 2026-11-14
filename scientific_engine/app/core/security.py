"""
Internal Security & Token Verification for Laravel-FastAPI communication
"""
from fastapi import HTTPException, Security, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from app.config import settings

security_bearer = HTTPBearer(auto_error=False)

def verify_internal_token(credentials: HTTPAuthorizationCredentials = Security(security_bearer)) -> bool:
    """
    Verifica que la solicitud provenga de Laravel o cliente autorizado con el token secreto.
    """
    if not settings.secret_token:
        return True  # Modo permisivo si no se configura token
    
    if credentials is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token de autorización faltante. Se requiere cabecera Bearer.",
            headers={"WWW-Authenticate": "Bearer"},
        )
    
    if credentials.credentials != settings.secret_token:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Token interno inválido para el motor científico SPACE VISION.",
        )
    return True
