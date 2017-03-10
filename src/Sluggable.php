<?php namespace Corazzi\Sluggable;

trait Sluggable
{
    /**
     * How many times we've attempted to create a slug
     *
     * @var int
     */
    private $slugIteration = 0;

    /**
     * Boot the Sluggable trait
     */
    public static function bootSluggable()
    {
        self::creating(function ($model) {
            if (! $model->hasSlug()) {
                $model->setSlug();
            }
        });
    }

    /**
     * Check if the model already has a slug set
     *
     * @return bool
     */
    public function hasSlug()
    {
        return !! $this->getAttributeValue(
            $this->getSlugColumn()
        );
    }

    /**
     * Set the model's slug
     *
     * @param null $slug
     */
    public function setSlug($slug = null)
    {
        $this->setAttribute(
            $this->getSlugColumn(),
            $slug ?: $this->generateSlug()
        );
    }

    /**
     * Check if the slug already exists for the model
     *
     * @param $slug
     *
     * @return bool
     */
    public function slugExists($slug)
    {
        return $this->where(
            $this->getSlugColumn(),
            $slug
        )->exists();
    }

    /**
     * Get the column name for the slug
     *
     * @return string
     */
    public function getSlugColumn()
    {
        return $this->slugColumn ?: 'slug';
    }

    /**
     * Get the name of the column that the slug will be generated from
     *
     * @return string
     */
    public function getSlugOriginColumn()
    {
        return $this->slugOrigin ?: 'name';
    }

    /**
     * Get the value of the column to be slugged
     *
     * @throws EmptyOriginException
     *
     * @return string
     */
    public function getSlugOrigin()
    {
        $origin = $this->getAttributeValue(
            $this->getSlugOriginColumn()
        );

        if (empty($origin)) {
            throw new EmptyOriginException('Slug origin is empty');
        }

        return $origin;
    }

    /**
     * Generate and return a unique slug
     *
     * @param string $append
     *
     * @return string
     */
    public function generateSlug($append = '')
    {
        // Generate the slug based on the name column
        $slug = str_slug(
            sprintf(
                '%s%s',
                $this->getSlugOrigin(),
                $append
            ),
            '-'
        );

        // Check if it exists - if it does, recursively call this method with a suffix
        if ($this->slugExists($slug)) {
            return $this->generateSlug(
                $this->getSlugSuffix()
            );
        }

        return $slug;
    }

    /**
     * If 'my-slug' already exists, try 'my-slug-1', 'my-slug-2', etc.
     *
     * @return int
     */
    public function getSlugSuffix()
    {
        return '-' . ++$this->slugIteration;
    }
}