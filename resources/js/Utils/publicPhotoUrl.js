// These existing uploads are brand or illustration assets, not meal or kitchen photos.
const nonPhotographicAssets = new Set([
    'Gb7iCCJLho8RmSFMoR6TWG6vZYFauCgECPys8hfe.png',
    'N7ykofeAmzuDuOIqGmwTFu5BPFqKFP01iCyy4E9z.png',
    'uSAqIq9aNNRWsR2KjEcxPbiy1AH9Mn2wyEeXdTj6.png',
    'hkTU20wZxDSxlVHoNCgv3cs4joQzQlUfjthRutIm.png',
    'uSzUiCijzCt0j1rP5YOXLJvDFNSXlynh8kbWomEY.png',
    'vpfByiJ1JMgPsNPtIgx3PlzIwXTYub6hPxMyU44a.png',
]);

export const getPublicPhotoUrl = (path) => {
    if (typeof path !== 'string' || !path.trim()) return null;

    const photoPath = path.trim();
    const fileName = photoPath.split(/[?#]/, 1)[0].split('/').pop();

    if (nonPhotographicAssets.has(fileName)) return null;

    if (/^https?:\/\//i.test(photoPath) || photoPath.startsWith('/')) {
        return photoPath;
    }

    return `/storage/${photoPath}`;
};
